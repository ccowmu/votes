#!/bin/bash

for position in positions/*;
do
  position=${position#positions/}
  echo votes for $position:
  find votes/ -name "$position" -execdir cat '{}' \; | sort | uniq -c
done
