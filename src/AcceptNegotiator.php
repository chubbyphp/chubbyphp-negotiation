<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.1
 */
final class AcceptNegotiator implements AcceptNegotiatorInterface
{
    /**
     * @var array<string>
     */
    private $supportedMediaTypes;

    /**
     * @param array<string> $supportedMediaTypes
     */
    public function __construct(array $supportedMediaTypes)
    {
        $this->supportedMediaTypes = $supportedMediaTypes;
    }

    /**
     * @return array<string>
     */
    public function getSupportedMediaTypes(): array
    {
        return $this->supportedMediaTypes;
    }

    /**
     * @param Request $request
     *
     * @return NegotiatedValueInterface|null
     */
    public function negotiate(Request $request)
    {
        if ([] === $this->supportedMediaTypes) {
            return null;
        }

        if (!$request->hasHeader('Accept')) {
            return null;
        }

        $aggregatedValues = $this->aggregatedValues($request->getHeaderLine('Accept'));

        return $this->compareAgainstSupportedMediaTypes($aggregatedValues);
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
            $mediaType = trim(array_shift($headerValueParts));
            $attributes = [];
            foreach ($headerValueParts as $attribute) {
                list($attributeKey, $attributeValue) = explode('=', $attribute);
                $attributes[trim($attributeKey)] = trim($attributeValue);
            }

            if (!isset($attributes['q'])) {
                $attributes['q'] = '1.0';
            }

            $values[$mediaType] = $attributes;
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
    private function compareAgainstSupportedMediaTypes(array $aggregatedValues)
    {
        if (null !== $negotiatedValue = $this->exactCompareAgainstSupportedMediaTypes($aggregatedValues)) {
            return $negotiatedValue;
        }

        if (null !== $negotiatedValue = $this->typeCompareAgainstSupportedMediaTypes($aggregatedValues)) {
            return $negotiatedValue;
        }

        if (isset($aggregatedValues['*/*'])) {
            return new NegotiatedValue(reset($this->supportedMediaTypes), $aggregatedValues['*/*']);
        }

        return null;
    }

    /**
     * @param array<string, array> $aggregatedValues
     *
     * @return NegotiatedValueInterface|null
     */
    private function exactCompareAgainstSupportedMediaTypes(array $aggregatedValues)
    {
        foreach ($aggregatedValues as $mediaType => $attributes) {
            if ('*/*' === $mediaType) {
                continue;
            }

            if (in_array($mediaType, $this->supportedMediaTypes, true)) {
                return new NegotiatedValue($mediaType, $attributes);
            }
        }

        return null;
    }

    /**
     * @param array<string, array> $aggregatedValues
     *
     * @return NegotiatedValueInterface|null
     */
    private function typeCompareAgainstSupportedMediaTypes(array $aggregatedValues)
    {
        foreach ($aggregatedValues as $mediaType => $attributes) {
            if ('*/*' === $mediaType) {
                continue;
            }

            $mediaTypeParts = explode('/', $mediaType);
            if (2 !== count($mediaTypeParts)) {
                continue; // skip invalid value
            }

            list($type, $subType) = $mediaTypeParts;

            if ('*' === $type || '*' !== $subType) {
                continue; // skip invalid value
            }

            foreach ($this->supportedMediaTypes as $supportedMediaType) {
                if (1 === preg_match('/^'.preg_quote($type).'\/.+$/', $supportedMediaType)) {
                    return new NegotiatedValue($supportedMediaType, $attributes);
                }
            }
        }

        return null;
    }
}
