<?php

declare(strict_types=1);

namespace Chubbyphp\Negotiation;

use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * @see https://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.17
 */
final class ContentTypeNegotiator implements ContentTypeNegotiatorInterface
{
    /**
     * @var array<int, string>
     */
    private array $supportedMediaTypes;

    /**
     * @param array<int, string> $supportedMediaTypes
     */
    public function __construct(array $supportedMediaTypes)
    {
        $this->supportedMediaTypes = $supportedMediaTypes;
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

        if (!$request->hasHeader('Content-Type')) {
            return null;
        }

        return $this->compareMediaTypes($request->getHeaderLine('Content-Type'));
    }

    private function compareMediaTypes(string $header): ?NegotiatedValueInterface
    {
        if (false !== strpos($header, ',')) {
            return null;
        }

        $headerValueParts = explode(';', $header);
        $mediaType = trim(array_shift($headerValueParts));
        $attributes = [];
        foreach ($headerValueParts as $attribute) {
            [$attributeKey, $attributeValue] = explode('=', $attribute);
            $attributes[trim($attributeKey)] = trim($attributeValue);
        }

        if (in_array($mediaType, $this->supportedMediaTypes, true)) {
            return new NegotiatedValue($mediaType, $attributes);
        }

        if (null !== $negotiatedValue = $this->compareMediaTypeWithSuffix($mediaType, $attributes)) {
            return $negotiatedValue;
        }

        return null;
    }

    /**
     * @param array<string, string> $attributes
     */
    private function compareMediaTypeWithSuffix(string $mediaType, array $attributes): ?NegotiatedValueInterface
    {
        $mediaTypeParts = [];
        if (1 !== preg_match('#^([^/]+)/([^+]+)\+(.+)$#', $mediaType, $mediaTypeParts)) {
            return null;
        }

        $mediaType = $mediaTypeParts[1].'/'.$mediaTypeParts[3];

        if (!in_array($mediaType, $this->supportedMediaTypes, true)) {
            return null;
        }

        return new NegotiatedValue($mediaType, $attributes);
    }
}
