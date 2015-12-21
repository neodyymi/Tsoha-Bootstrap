#!/bin/bash

source config/environment.sh

ssh $USERNAME@users.cs.helsinki.fi '
tail -f /home/userlogs/$USER.error'
