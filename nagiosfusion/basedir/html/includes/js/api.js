
function gethosts(){var hosturl="/nagiosfusion/includes/components/xidata/xidata.php?cmd=gethosts";var sid=$("#sid").val();$("#host").load(hosturl+"&sid="+sid);}
function getservices(){var serviceurl="/nagiosfusion/includes/components/xidata/xidata.php?cmd=getservices";var sid=$("#sid").val();var host=$("#host").val();$("#service").load(serviceurl+"&sid="+sid+"&host="+encodeURI(host));}
function gethostgroups(){var hostgroupurl="/nagiosfusion/includes/components/xidata/xidata.php?cmd=gethostgroups";var sid=$("#sid").val();$("#hostgroup").load(hostgroupurl+"&sid="+sid);}
function getservicegroups(){var servicegroupurl="/nagiosfusion/includes/components/xidata/xidata.php?cmd=getservicegroups";var sid=$("#sid").val();$("#servicegroup").load(servicegroupurl+"&sid="+sid);}