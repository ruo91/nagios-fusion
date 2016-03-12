#!/bin/sh -e

psql -c "create user nagiosfusion with password 'n@gweb';"
psql -c "create database nagiosfusion owner nagiosfusion;"

