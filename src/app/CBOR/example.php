<?php
include("CBOREncoder.php");
include("CBORExceptions.php");
include("Types/CBORByteString.php");

function base64_urlsafe_decode($val)
{
    $val = str_replace(array('_', '-', '.'), array('+', '/', '='), $val);

    return base64_decode($val);
}
//encoded string
$encoded_data = base64_urlsafe_decode('o2NmbXRmcGFja2VkZ2F0dFN0bXSiY2FsZyZjc2lnWEgwRgIhAJH6hJ5r6kCQgUkRuvCDAnO4h05EBVvk_DUJYaGc5i1WAiEAhNaTRoesJ8VwAAZPYWuhBpkin92xIizrH_0M8m4VV4loYXV0aERhdGFYvUmWDeWIDoxodDQXD2R2YFuP5K65ooYyx5lc87qDHZdjRWIYWfOtzgACNbzGCmSLCyXx8FUDADkB19FZCPfF7jiXQaJqmJb8CNYXOZ8FMRE9JGQcTj-Hxs8bGlsmfZkVx3YWi2N1fhhlO9v_JOET-K2lAQIDJiABIVgg8N69tzz8e-GxHxF_d63BsGxl7s1ltb5rSPlp5nzgPBYiWCB5D886e8BBjQWCVrqRpcQFX5Qv2F3rE_nsAQljMG1RyQ');

//debug info output
/*$byte_arr = unpack("C*", $encoded_data);

echo "Byte hex map = " . implode(" ", array_map(function($byte){
        return "0x" . strtoupper(dechex($byte));
    }, $byte_arr)) . PHP_EOL;

echo "Byte dec map = " . implode(" ", $byte_arr) . PHP_EOL;
*/

//decode
$decoded_variable = \CBOR\CBOREncoder::decode($encoded_data);
//output
var_dump($decoded_variable);
