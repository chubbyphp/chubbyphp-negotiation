<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.4
 */
final class AcceptLanguageNegotiator implements AcceptLanguageNegotiatorInterface
{
    /**
     * @var array<int, string>
     */
    private $supportedLocales;

    /**
     * @param array<int, string> $supportedLocales
     */
    public function __construct(array $supportedLocales)
    {
        $this->supportedLocales = $supportedLocales;
    }

    /**
     * @return array<int, string>
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    public function negotiate(Request $request): ?NegotiatedValueInterface
    {
        if ([] === $this->supportedLocales) {
            return null;
        }

        if (!$request->hasHeader('Accept-Language')) {
            return null;
        }

        $acceptLanguages = $this->acceptLanguages($request->getHeaderLine('Accept-Language'));

        return $this->compareAcceptLanguages($acceptLanguages);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function acceptLanguages(string $header): array
    {
        $values = [];
        foreach (explode(',', $header) as $headerValue) {
            $headerValueParts = explode(';', $headerValue);
            $locale = trim(array_shift($headerValueParts));
            $attributes = [];
            foreach ($headerValueParts as $attribute) {
                list($attributeKey, $attributeValue) = explode('=', $attribute);
                $attributes[trim($attributeKey)] = trim($attributeValue);
            }

            if (!isset($attributes['q'])) {
                $attributes['q'] = '1.0';
            }

            $values[$locale] = $attributes;
        }

        uasort($values, static function (array $valueA, array $valueB) {
            return $valueB['q'] <=> $valueA['q'];
        });

        return $values;
    }

    /**
     * @param array<string, array<string, string>> $acceptLanguages
     */
    private function compareAcceptLanguages(array $acceptLanguages): ?NegotiatedValueInterface
    {
        foreach ($acceptLanguages as $locale => $attributes) {
            if (in_array($locale, $this->supportedLocales, true)) {
                return new NegotiatedValue($locale, $attributes);
            }
        }

        foreach ($acceptLanguages as $locale => $attributes) {
            if (null !== $negotiatedValue = $this->compareLanguage($locale, $attributes)) {
                return $negotiatedValue;
            }
        }

        if (isset($acceptLanguages['*'])) {
            return new NegotiatedValue(reset($this->supportedLocales), $acceptLanguages['*']);
        }

        return null;
    }

    /**
     * @param array<string, string> $attributes
     */
    private function compareLanguage(string $locale, array $attributes): ?NegotiatedValueInterface
    {
        if (1 !== preg_match('#^([^-]+)-([^-]+)$#', $locale, $localeParts)) {
            return null;
        }

        $language = $localeParts[1];

        if (!in_array($language, $this->supportedLocales, true)) {
            return null;
        }

        return new NegotiatedValue($language, $attributes);
    }
}
