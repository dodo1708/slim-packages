<?php

declare(strict_types=1);

namespace SlimAP\ProcessingConfig;

use SlimAP\Preprocessor\PreprocessorInterface;

class AbstractProcessingConfig
{
    final public const TYPE_INT = 'int';
    final public const TYPE_PINT = 'pint';
    final public const TYPE_NINT = 'nint';
    final public const TYPE_MAXINT = 'maxint';
    final public const TYPE_MININT = 'minint';
    final public const TYPE_STEPINT = 'stepint';

    final public const TYPE_FLOAT = 'float';
    final public const TYPE_PFLOAT = 'pfloat';
    final public const TYPE_NFLOAT = 'nfloat';
    final public const TYPE_MAXFLOAT = 'maxfloat';
    final public const TYPE_MINFLOAT = 'minfloat';
    final public const TYPE_STEPFLOAT = 'stepfloat';

    final public const TYPE_STRING = 'string';
    final public const TYPE_TRSTRING = 'trstring';
    final public const TYPE_MAXSTRING = 'maxstring';
    final public const TYPE_UCSTRING = 'ucstring';
    final public const TYPE_LCSTRING = 'lcstring';
    final public const TYPE_SAFESTRING = 'safestring';
    final public const TYPE_DATESTRING = 'datestring';

    final public const TYPE_BOOL = 'bool';

    private array $config = [];

    public function addPreprocessor(PreprocessorInterface $preprocessor): static
    {
        $this->config[$preprocessor::getIdentifier()] = $preprocessor;
        return $this;
    }

    public function apply(mixed $data): mixed
    {
        /** @var PreprocessorInterface $preprocessor */
        foreach ($this->config as $identifier => $preprocessor) {
            $data = $preprocessor->process($data);
        }
        return $data;
    }
}
