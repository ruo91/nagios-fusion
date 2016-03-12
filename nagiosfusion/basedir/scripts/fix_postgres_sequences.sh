#!/bin/sh

echo "Nagios Fusion Postgres Database Sequence Information"
echo ""
echo "OLD VALUES"
echo "--------------"
for seq in fusion_commands_command_id_seq fusion_events_event_id_seq fusion_meta_meta_id_seq fusion_options_option_id_seq fusion_sysstat_sysstat_id_seq fusion_usermeta_usermeta_id_seq fusion_users_user_id_seq ; do
   val=`psql -U nagiosfusion nagiosfusion -q -t -A -c "SELECT last_value FROM $seq"`
   echo "$seq = $val"
done
echo ""

# fusion_options
seq="fusion_options_option_id_seq"
goodval=99
val=`psql -U nagiosfusion nagiosfusion -q -t -A -c "SELECT last_value FROM $seq"`
if [ $val -lt $goodval ] ; then
	`psql -U nagiosfusion nagiosfusion -q -t -A -c "ALTER SEQUENCE $seq RESTART WITH $goodval"`
fi

# fusion_sysstat
seq="fusion_sysstat_sysstat_id_seq"
goodval=99
val=`psql -U nagiosfusion nagiosfusion -q -t -A -c "SELECT last_value FROM $seq"`
if [ $val -lt $goodval ] ; then
	`psql -U nagiosfusion nagiosfusion -q -t -A -c "ALTER SEQUENCE $seq RESTART WITH $goodval"`
fi

# fusion_users
seq="fusion_users_user_id_seq"
goodval=49
val=`psql -U nagiosfusion nagiosfusion -q -t -A -c "SELECT last_value FROM $seq"`
if [ $val -lt $goodval ] ; then
	`psql -U nagiosfusion nagiosfusion -q -t -A -c "ALTER SEQUENCE $seq RESTART WITH $goodval"`
fi

# fusion_usermeta
seq="fusion_usermeta_usermeta_id_seq"
goodval=399
val=`psql -U nagiosfusion nagiosfusion -q -t -A -c "SELECT last_value FROM $seq"`
if [ $val -lt $goodval ] ; then
	`psql -U nagiosfusion nagiosfusion -q -t -A -c "ALTER SEQUENCE $seq RESTART WITH $goodval"`
fi

echo "NEW VALUES"
echo "--------------"
for seq in fusion_commands_command_id_seq fusion_events_event_id_seq fusion_meta_meta_id_seq fusion_options_option_id_seq fusion_sysstat_sysstat_id_seq fusion_usermeta_usermeta_id_seq fusion_users_user_id_seq ; do
   val=`psql -U nagiosfusion nagiosfusion -q -t -A -c "SELECT last_value FROM $seq"`
   echo "$seq = $val"
done
echo ""
