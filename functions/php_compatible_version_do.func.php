<?php

// php_compatible_version_do('function', 'function', '5.3.0');
function php_compatible_version_do($newest = null, $oldest = null, $version = '5.3.0') {
    if (version_compare(PHP_VERSION, $version, '>=')) {
        if (!is_null($newest)) {
            return $newest();
        }
    } else {
        if (!is_null($oldest)) {
            return $oldest();
        }
    }
}

// _pcvd('function', 'function', '5.3.0');
function _pcvd($newest = null, $oldest = null, $version = '5.3.0') {
    return php_compatible_version_do($newest, $oldest, $version);
}

?>