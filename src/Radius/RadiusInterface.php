<?php


namespace Meklis\RadiusToNodeny\Radius;


use Meklis\RadiusToNodeny\Radius\Acct\Request as RadAcct;
use Meklis\RadiusToNodeny\Radius\PostAuth\Request as PostRequest;
use Meklis\RadiusToNodeny\Radius\Auth\Request as ReplyRequest;
use Meklis\RadiusToNodeny\Radius\Auth\Response;

interface RadiusInterface
{
    function radReply(ReplyRequest $req): Response;
    function radPostAuth(PostRequest $req);
    function radAcct(RadAcct $req);
}