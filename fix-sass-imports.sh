#!/bin/bash

# Ruta base donde están los archivos SCSS
BASE_DIR="resources/assets"

# Encuentra todos los .scss que usan @import y los modifica
find "$BASE_DIR" -name "*.scss" | while read file; do
    echo "Procesando: $file"
    sed -i '' -E 's/@import\s+"([^"]+)";/@use "\1";/g' "$file"
done

echo "✔️ Todos los @import fueron reemplazados por @use"
