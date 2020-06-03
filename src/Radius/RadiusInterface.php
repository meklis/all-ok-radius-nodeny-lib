<?php


namespace Meklis\RadiusToNodeny\Radius;


use Meklis\RadiusToNodeny\Radius\PostAuth\Request as PostRequest;
use Meklis\RadiusToNodeny\Radius\RadReply\Request as ReplyRequest;
use Meklis\RadiusToNodeny\Radius\RadReply\Response;

interface RadiusInterface
{
    function radReply(ReplyRequest $req): Response;
    function radPostAuth(PostRequest $req);
}