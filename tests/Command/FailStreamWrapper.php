<?php

namespace Tests\Command;

class FailStreamWrapper
{
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        return false;
    }

    public function stream_stat()
    {
        return ['mode' => 0100444, 'size' => 0]; // File, readable
    }

    public function url_stat($path, $flags)
    {
        return ['mode' => 0100444, 'size' => 0]; // File, readable
    }

    public function stream_read($count)
    {
        return false;
    }

    public function stream_eof()
    {
        return true;
    }

    public function stream_set_option($option, $arg1, $arg2)
    {
        return false;
    }
}
