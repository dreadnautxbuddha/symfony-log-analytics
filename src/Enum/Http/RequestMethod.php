<?php

namespace Dreadnaut\LogAnalyticsBundle\Enum\Http;

enum RequestMethod: string
{
    case GET = 'GET';
    case HEAD = 'HEAD';
    case POST = 'POST';
    case PUT = 'PUT';
    case DELETE = 'DELETE';
    case CONNECT = 'CONNECT';
    case OPTIONS = 'OPTIONS';
    case TRACE = 'TRACE';
    case PATCH = 'PATCH';
    case PURGE = 'PURGE';
}
