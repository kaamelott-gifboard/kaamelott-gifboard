#!/bin/sh

# This script will receive PROCESS_STATE_STOPPED, PROCESS_STATE_EXITED and
# PROCESS_STATE_FATAL events from supervisord
#
# * PROCESS_STATE_STOPPED => Indicates a process has moved from the STOPPING state to the STOPPED state.
# * PROCESS_STATE_EXITED => Indicates a process has moved from the RUNNING state to the EXITED state.
# * PROCESS_STATE_FATAL => Indicates a process has moved from the BACKOFF state to the FATAL state.
#                          This means that Supervisor tried startretries number of times unsuccessfully to start the
#                          process, and gave up attempting to restart it.

# Tell supervisord we're ready to receive events
printf "READY\n";

while read -r; do
  # Kill supervisord (will stop the container)
  kill -s SIGTERM "$(cat /run/supervisord/supervisord.pid)"
done < /dev/stdin
