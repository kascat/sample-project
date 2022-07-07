#!/usr/bin/env bash
(cd backend && git pull origin dev) &
(cd frontend && git pull origin dev)
