<?php

include('Net/SSH2.php');

$ip   = '191.96.139.176';
$user = 'root';
$pass = '4nroR6DFWd3zXfl';

$ssh  = new Net_SSH2($ip);

if (!$ssh->login($user, $pass)) {
    exit('Login Failed');
}

function findPids($ssh) {
	$npids   = [];
	$pidsPhp = $ssh->exec('pgrep -l php');

	if(stristr($pidsPhp, 'php')) {
		$pidsPhp = explode("\n", $pidsPhp);
		foreach($pidsPhp as $pid){
			$pid = str_replace([' ', 'php'], '', $pid);
			$pid = trim(rtrim($pid));
			if(strlen($pid) > 0){
				$npids[] = $pid;
			}
		}
	}
	return $npids;
}

function killPids($pids, $ssh) {

	if(!is_array($pids)) {
		return false;
	}

	foreach($pids as $pid) {
		echo $ssh->exec("kill -09 {$pid}");
	}
	return true;
}

function run($ssh) {
	echo $ssh->exec('cd /var/www/html/demo && ./run.sh');
	echo $ssh->exec('cd /var/www/html/demonew && ./run.sh');
}

$comando = $argv[1] ?? '';

if($comando == 'start') {
	echo "start Aguarde.....\n";
	$ver = run($ssh);
	echo 'Start Ok.' . PHP_EOL;
	die;
}elseif($comando == 'stop') {
	echo "stop Aguarde.....\n";
	$pids = findPids($ssh);
	$kill = killPids($pids, $ssh);
	if($kill) {
		echo 'Stop ok' . PHP_EOL;
	}else{
		echo 'Stop error' . PHP_EOL;
	}
}elseif($comando == 'restart') {
	echo "restart Aguarde.....\n";
	$pids = findPids($ssh);
	$kill = killPids($pids, $ssh);
	if($kill) {
		$pids = findPids($ssh);
		if(count($pids) === 0) {
			$ver = run($ssh);
			echo 'Restart Ok ' . PHP_EOL;
		}
	}else{
		echo 'Restart error' . PHP_EOL;
	}
}elseif($comando == 'status') {
	echo "status Aguarde.....\n";
	$pids = findPids($ssh);
	echo "PIDS php ativos:\n";
	print_r($pids);
	die;
}else{
	echo "
\n\n############################## Escolha uma opção: ##############################\n\n
\t\tphp main.php start\t ==\tstart
\t\tphp main.php stop\t ==\tstop
\t\tphp main.php restart\t ==\trestart
\t\tphp main.php status\t ==\tstatus pids.
\n\n\n############################## ------------------ ##############################

";
}

// /$pids = findPids($ssh);
//$kill = killPids($pids, $ssh);
//print_r($kill);
//echo "fim kill\n";
//$pids = findPids($ssh);

//print_r($pids);
//$pidsPhp = explod
//print_r($pidsPhp);
