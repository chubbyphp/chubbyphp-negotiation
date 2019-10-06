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
     * @var array<string>
     */
    private $supportedLocales;

    /**
     * @param array<string> $supportedLocales
     */
    public function __construct(array $supportedLocales)
    {
        $this->supportedLocales = $supportedLocales;
    }

    /**
     * @return array<string>
     */
    public function getSupportedLocales(): array
    {
        return $this->supportedLocales;
    }

    /**
     * @param Request $request
     *
     * @return NegotiatedValueInterface|null
     */
    public function negotiate(Request $request)
    {
        if ([] === $this->supportedLocales) {
            return null;
        }

        if (!$request->hasHeader('Accept-Language')) {
            return null;
        }

        $aggregatedValues = $this->aggregatedValues($request->getHeaderLine('Accept-Language'));

        return $this->compareAgainstSupportedLocales($aggregatedValues);
    }

    /**
     * @param string $header
     *
     * @return array<string, array>
     */
    private function aggregatedValues(string $header): array
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

        uasort($values, function (array $a, array $b) {
            return $b['q'] <=> $a['q'];
        });

        return $values;
    }

    /**
     * @param array<string, array> $aggregatedValues
     *
     * @return NegotiatedValueInterface|null
     */
    private function compareAgainstSupportedLocales(array $aggregatedValues)
    {
        if (null !== $negotiatedValue = $this->exactCompareAgainstSupportedLocales($aggregatedValues)) {
            return $negotiatedValue;
        }

        if (null !== $negotiatedValue = $this->languageCompareAgainstSupportedLocales($aggregatedValues)) {
            return $negotiatedValue;
        }

        if (isset($aggregatedValues['*'])) {
            return new NegotiatedValue(reset($this->supportedLocales), $aggregatedValues['*']);
        }

        return null;
    }

    /**
     * @param array<string, array> $aggregatedValues
     *
     * @return NegotiatedValueInterface|null
     */
    private function exactCompareAgainstSupportedLocales(array $aggregatedValues)
    {
        foreach ($aggregatedValues as $locale => $attributes) {
            if (in_array($locale, $this->supportedLocales, true)) {
                return new NegotiatedValue($locale, $attributes);
            }
        }

        return null;
    }

    /**
     * @param array<string, array> $aggregatedValues
     *
     * @return NegotiatedValueInterface|null
     */
    private function languageCompareAgainstSupportedLocales(array $aggregatedValues)
    {
        foreach ($aggregatedValues as $locale => $attributes) {
            $localeParts = explode('-', $locale);
            if (2 !== count($localeParts)) {
                continue;
            }

            $language = $localeParts[0];

            if (in_array($language, $this->supportedLocales, true)) {
                return new NegotiatedValue($language, $attributes);
            }
        }

        return null;
    }
}
