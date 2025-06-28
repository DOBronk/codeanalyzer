#!/bin/bash

host="127.0.0.1"
port=3306

# Controleer of de verbinding tot stand kan worden gebracht
if exec 3<>/dev/tcp/$host/$port; then
  echo "Verbinding met $host:$port succesvol"
  echo "Verzenden van gegevens..."
  echo "GET / HTTP/1.1" >&3
  echo "Host: $host" >&3
  echo "" >&3

  # Lees de reactie
  while IFS= read -r line; do
    echo "$line"
  done <&3
  exec 3<&- # Sluit de verbinding
else
  echo "Fout: kon geen verbinding maken met $host:$port"
fi
