#!/bin/bash

# Отримуємо шлях до поточної папки скрипта
SCRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"

# Функція для перевірки і установки пакетів
check_and_install_packages() {
    # Перевіряємо, чи встановлено rsync
    if ! command -v rsync &> /dev/null; then
        echo "rsync не встановлено. Встановлюємо..."
        sudo apt-get update && sudo apt-get install -y rsync
    else
        echo "rsync вже встановлено."
    fi

    # Перевіряємо, чи встановлено inotifywait
    if ! command -v inotifywait &> /dev/null; then
        echo "inotifywait не встановлено. Встановлюємо..."
        sudo apt-get update && sudo apt-get install -y inotify-tools
    else
        echo "inotifywait вже встановлено."
    fi

    # Перевіряємо, чи встановлено jq
    if ! command -v jq &> /dev/null; then
        echo "jq не встановлено. Встановлюємо..."
        sudo apt-get update && sudo apt-get install -y jq
    else
        echo "jq вже встановлено."
    fi
}

# Викликаємо функцію для перевірки і встановлення пакетів
check_and_install_packages

# Змінні для шляхів
SRC="$SCRIPT_DIR/upload/"
DEST="$SCRIPT_DIR/../../"
JSON_FILE="$SCRIPT_DIR/opencart-module.json"

# Перевірка, чи існує JSON файл, і створення порожнього масиву, якщо ні
if [ ! -f "$JSON_FILE" ]; then
    echo "[]" > "$JSON_FILE"
fi

# Функція для одноразової синхронізації всіх файлів
sync_files_build() {
    echo "Запущено одноразову синхронізацію файлів..."

    # Синхронізуємо всі файли та папки з плагіна до OpenCart та отримуємо список файлів
    FILES=$(rsync -av "$SRC" "$DEST" --out-format="%f")

    # Додаємо нові файли до JSON файлу без дублювання
    echo "$FILES" | jq -R -s -c 'split("\n")[:-1]' | jq -s 'add | unique' "$JSON_FILE" - > temp.json && mv temp.json "$JSON_FILE"

    echo "Одноразова синхронізація завершена."
}

# Функція для безперервної синхронізації та відстеження змін
sync_files_dev() {
    echo "Запущено безперервну синхронізацію файлів з відстеженням змін..."

    # Відстежуємо зміни у вихідній папці
    inotifywait -m -r -e modify,create,delete "$SRC" | while read -r path _ file; do
        # Синхронізуємо всі файли та папки з плагіна до OpenCart
        FILES=$(rsync -av "$SRC" "$DEST" --out-format="%f")

        # Додаємо нові файли до JSON файлу без дублювання
        echo "$FILES" | jq -R -s -c 'split("\n")[:-1]' | jq -s 'add | unique' "$JSON_FILE" - > temp.json && mv temp.json "$JSON_FILE"

        echo "Синхронізовано: $file"
    done
}

# Функція для видалення всіх файлів модуля на основі JSON файлу
delete_files() {
    if [ -f "$JSON_FILE" ]; then
        echo "Запущено видалення файлів на основі JSON файлу..."

        # Зчитуємо список файлів з JSON і видаляємо кожен з них
        for file in $(jq -r '.[]' "$JSON_FILE"); do
            rm -f "$DEST$file"
            echo "Видалено: $DEST$file"
        done

        echo "Всі файли модуля видалено."
    else
        echo "Файл JSON не знайдено!"
    fi
}

# Основний блок для обробки аргументів командного рядка
case "$1" in
    build)
        sync_files_build
        ;;
    dev)
        sync_files_dev
        ;;
    delete)
        delete_files
        ;;
    *)
        echo "Невірний аргумент. Використовуйте:"
        echo "  build       - для одноразової синхронізації"
        echo "  dev         - для безперервної синхронізації з відстеженням змін"
        echo "  delete      - для видалення файлів модуля з OpenCart"
        ;;
esac

