<?php

namespace App\Http\Actions;

class LongPollingAction implements ActionInterface
{
    private int $maxExecutionTime;
    private int $maxInputTime;
    private int $requestTerminateTimeout;

    public function after(array $params)
    {
        ini_set('max_execution_time', $this->maxExecutionTime);
        ini_set('max_input_time', $this->maxInputTime);
        ini_set('request_terminate_timeout', $this->requestTerminateTimeout);
    }

    public function before(array $params)
    {
        $this->maxExecutionTime = (int) ini_get('max_execution_time');
        $this->maxInputTime = (int) ini_get('max_input_time');
        $this->requestTerminateTimeout = (int) ini_get('request_terminate_timeout');

        ini_set('max_execution_time', $params['maxwait'] ?? 3600);
        ini_set('max_input_time', $params['maxwait'] ?? 3600);
        ini_set('request_terminate_timeout', $params['maxwait'] ?? 3600);
    }
}