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
     * @var array
     */
    private $supportedMediaTypes;

    /**
     * @param array $supportedMediaTypes
     */
    public function __construct(array $supportedMediaTypes)
    {
        $this->supportedMediaTypes = $supportedMediaTypes;
    }

    /**
     * @return string[]
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

        if (!$request->hasHeader('Content-Type')) {
            return null;
        }

        return $this->compareAgainstSupportedMediaTypes($request->getHeaderLine('Content-Type'));
    }

    /**
     * @param string $header
     *
     * @return NegotiatedValueInterface|null
     */
    private function compareAgainstSupportedMediaTypes(string $header)
    {
        if (false !== strpos($header, ',')) {
            return null;
        }

        $headerValueParts = explode(';', $header);
        $mediaType = trim(array_shift($headerValueParts));
        $attributes = [];
        foreach ($headerValueParts as $attribute) {
            list($attributeKey, $attributeValue) = explode('=', $attribute);
            $attributes[trim($attributeKey)] = trim($attributeValue);
        }

        if (in_array($mediaType, $this->supportedMediaTypes, true)) {
            return new NegotiatedValue($mediaType, $attributes);
        }

        return null;
    }
}
