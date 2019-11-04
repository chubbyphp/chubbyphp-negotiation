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
     * @var array<int, string>
     */
    private $supportedMediaTypes;

    /**
     * @var array<int, string>
     */
    private $suffixBasedSupportedMediaTypes;

    /**
     * @param array<int, string> $supportedMediaTypes
     */
    public function __construct(array $supportedMediaTypes)
    {
        $this->supportedMediaTypes = [];
        $this->suffixBasedSupportedMediaTypes = [];

        foreach ($supportedMediaTypes as $index => $supportedMediaType) {
            $this->supportedMediaTypes[$index] = $supportedMediaType;

            $supportedMediaTypeParts = [];
            if (1 !== preg_match('#^([^/+]+)/([^/+]+)\+([^/+]+)$#', $supportedMediaType, $supportedMediaTypeParts)) {
                continue;
            }

            $this->suffixBasedSupportedMediaTypes[$index] = $supportedMediaTypeParts[1].'/'.$supportedMediaTypeParts[3];
        }
    }

    /**
     * @return array<int, string>
     */
    public function getSupportedMediaTypes(): array
    {
        return $this->supportedMediaTypes;
    }

    public function negotiate(Request $request): ?NegotiatedValueInterface
    {
        if ([] === $this->supportedMediaTypes) {
            return null;
        }

        if (!$request->hasHeader('Accept')) {
            return null;
        }

        $mediaTypes = $this->mediaTypes($request->getHeaderLine('Accept'));

        return $this->compareMediaTypes($mediaTypes);
    }

    /**
     * @return array<string, array<string, string>>
     */
    private function mediaTypes(string $header): array
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

        uasort($values, static function (array $valueA, array $valueB) {
            return $valueB['q'] <=> $valueA['q'];
        });

        return $values;
    }

    /**
     * @param array<string, array<string, string>> $mediaTypes
     */
    private function compareMediaTypes(array $mediaTypes): ?NegotiatedValueInterface
    {
        foreach ($mediaTypes as $mediaType => $attributes) {
            if (in_array($mediaType, $this->supportedMediaTypes, true)) {
                return new NegotiatedValue($mediaType, $attributes);
            }
        }

        foreach ($mediaTypes as $mediaType => $attributes) {
            if (null !== $negotiatedValue = $this->compareMediaTypeWithSuffix($mediaType, $attributes)) {
                return $negotiatedValue;
            }
        }

        foreach ($mediaTypes as $mediaType => $attributes) {
            if (null !== $negotiatedValue = $this->compareMediaTypeWithTypeOnly($mediaType, $attributes)) {
                return $negotiatedValue;
            }
        }

        if (isset($mediaTypes['*/*'])) {
            return new NegotiatedValue(reset($this->supportedMediaTypes), $mediaTypes['*/*']);
        }

        return null;
    }

    /**
     * @param array<string, string> $attributes
     */
    private function compareMediaTypeWithSuffix(string $mediaType, array $attributes): ?NegotiatedValueInterface
    {
        if (false !== $index = array_search($mediaType, $this->suffixBasedSupportedMediaTypes, true)) {
            return new NegotiatedValue($this->supportedMediaTypes[$index], $attributes);
        }

        return null;
    }

    /**
     * @param array<string, string> $attributes
     */
    private function compareMediaTypeWithTypeOnly(string $mediaType, array $attributes): ?NegotiatedValueInterface
    {
        $mediaTypeParts = [];
        if (1 !== preg_match('#^([^/+]+)/\*$#', $mediaType, $mediaTypeParts)) {
            return null;
        }

        foreach ($this->supportedMediaTypes as $supportedMediaType) {
            if (1 === preg_match('/^'.preg_quote($mediaTypeParts[1]).'\/.+$/', $supportedMediaType)) {
                return new NegotiatedValue($supportedMediaType, $attributes);
            }
        }

        return null;
    }
}
