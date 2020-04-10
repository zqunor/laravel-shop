<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;

class InvalidRequestException extends Exception
{
    public function __construct(string $msg = '', int $code = 400)
    {
        parent::__construct($msg, $code);
    }

    public function render(Request $request)
    {
        // 判断如果是 AJAX 请求则返回 JSON 格式的数据
        if ($request->expectsJson()) {
            // json() 方法第二个参数就是 Http 状态码
            return response()->json(['msg' => $this->message], $this->code);
        }

        // 否则就返回一个错误页面
        return view('pages.error', ['msg' => $this->message]);
    }
}
