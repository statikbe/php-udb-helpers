<?php

namespace statikbe\udb;

enum Environments
{
    case TEST;

    case PROD;
    
    public function getOAuthUrl(): string
    {
        return match ($this) {
            self::TEST => 'https://account-test.uitid.be',
            self::PROD => 'https://account.uitid.be',
        };
    }

    public function getEndpoint(): string
    {
        return match ($this) {
            self::TEST => 'https://io-test.uitdatabank.be',
            self::PROD => 'https://io.uitdatabank.be',
        };
    }

    public function getSearchEndpoint(): string
    {
        return match ($this) {
            self::TEST => 'https://search-test.uitdatabank.be',
            self::PROD => 'https://search.uitdatabank.be',
        };
    }
}

