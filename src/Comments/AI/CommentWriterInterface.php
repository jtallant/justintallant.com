<?php

namespace JustinTallant\Comments\AI;

interface CommentWriterInterface
{
    public function write(string $prompt, string $content): string;
}
