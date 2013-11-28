<?php
function smarty_function_fgc($params, &$smarty) {
	if (!isset($params['resource'])) {
		$smarty -> trigger_error("fgc: missing 'resource' parameter");
		return;
	}

	if (!isset($params['file'])) {
		$smarty -> trigger_error("fgc: missing 'file' parameter");
		return;
	}

	if (!isset($params['mime'])) {
		$smarty -> trigger_error("fgc: missing 'mime' parameter");
		return;
	}
	
	return file_get_contents($params['resource'] . $params['file'] . '.' . $params['mime']);
}
?> 