<?php

function tail($filename, $n)
{
    $buffer_size = 1024;

    if (!($fp = fopen($filename, 'r')))
        return array();

    fseek($fp, 0, SEEK_END);
    $pos = ftell($fp);

    $input = '';
    $line_count = 0;
    while ($line_count < $n + 1)
    {
        // read the previous block of input
        $read_size = $pos >= $buffer_size ? $buffer_size : $pos;
        fseek($fp, $pos - $read_size, SEEK_SET);

        // prepend the current block, and count the new lines
        $input = fread($fp, $read_size).$input;
        $line_count = substr_count(ltrim($input), "\r\n");

        // if $pos is == 0 we are at start of file
        $pos -= $read_size;
        if (!$pos)
            break;
    }
    fclose($fp);

    return array_slice(explode("\r\n", rtrim($input)), -$n);
}

?>