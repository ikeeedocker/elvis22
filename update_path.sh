#!/bin/bash

# Directory da aggiungere al PATH
NEW_DIR="/usr/local/go/bin"

# Verifica se la directory è già nel PATH
if [[ ":$PATH:" != *":$NEW_DIR:"* ]]; then
  # Aggiungi la directory al .bashrc se non è già presente
  echo "export PATH=\"\$PATH:$NEW_DIR\"" >> ~/.bashrc
  echo "La directory $NEW_DIR è stata aggiunta al PATH."
else
  echo "La directory $NEW_DIR è già presente nel PATH."
fi

