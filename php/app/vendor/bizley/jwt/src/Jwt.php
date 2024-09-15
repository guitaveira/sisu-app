<?php

declare(strict_types=1);

namespace bizley\jwt;

use Lcobucci\JWT as BaseJwt;
use yii\base\InvalidConfigException;

/**
 * JSON Web Token implementation based on lcobucci/jwt library v5.
 * @see https://github.com/lcobucci/jwt
 *
 * This implementation is based on the \Lcobucci\JWT\Configuration setup which requires both signing and verifying keys
 * to be defined (the standard way). If you need only some JWT tools, please use \bizley\jwt\JwtTools directly.
 *
 * @author Paweł Bizley Brzozowski <pawel.bizley@gmail.com> since 2.0 (fork)
 * @author Dmitriy Demin <sizemail@gmail.com> original package
 */
class Jwt extends JwtTools
{
    public const HS256 = 'HS256';
    public const HS384 = 'HS384';
    public const HS512 = 'HS512';
    public const RS256 = 'RS256';
    public const RS384 = 'RS384';
    public const RS512 = 'RS512';
    public const ES256 = 'ES256';
    public const ES384 = 'ES384';
    public const ES512 = 'ES512';
    public const EDDSA = 'EdDSA';
    public const BLAKE2B = 'BLAKE2B';

    public const METHOD_PLAIN = 'plain';
    public const METHOD_BASE64 = 'base64';
    public const METHOD_FILE = 'file';

    public const SYMMETRIC = 'symmetric';
    public const ASYMMETRIC = 'asymmetric';

    public const KEY = 'key';
    public const METHOD = 'method';
    public const PASSPHRASE = 'passphrase';

    /**
     * @var string|array<string, string>|BaseJwt\Signer\Key Signing key definition.
     * This can be a simple string, an instance of Key, or a configuration array.
     * The configuration takes the following array keys:
     * - 'key'        => Key's value or path to the key file.
     * - 'method'     => `Jwt::METHOD_PLAIN`, `Jwt::METHOD_BASE64`, or `Jwt::METHOD_FILE` - whether the key is a plain
     *                   text, base64 encoded text, or a file.
     * - 'passphrase' => Key's passphrase.
     * In case a simple string is provided (and it does not start with 'file://' or '@') the following configuration
     * is assumed:
     * [
     *      'key' => // the original given value,
     *      'method' => Jwt::METHOD_PLAIN,
     *      'passphrase' => '',
     * ]
     * In case a simple string is provided, and it does start with 'file://' (direct file path) or '@' (Yii alias)
     * the following configuration is assumed:
     * [
     *      'key' => // the original given value,
     *      'method' => Jwt::METHOD_FILE,
     *      'passphrase' => '',
     * ]
     * If you want to override the assumed configuration, you must provide it directly.
     * @since 3.0.0
     */
    public $signingKey = '';

    /**
     * @var string|array<string, string>|BaseJwt\Signer\Key Verifying key definition.
     * $signingKey documentation you can find above applies here as well.
     * Symmetric algorithms (like HMAC) use a single key to sign and verify tokens so this property is ignored in that
     * case. Asymmetric algorithms (like RSA and ECDSA) use a private key to sign and a public key to verify.
     * @since 3.0.0
     */
    public $verifyingKey = '';

    /**
     * @var string|BaseJwt\Signer Signer ID or Signer instance to be used for signing/verifying.
     * See $signers for available values. Since 4.0.0 it cannot be empty anymore.
     * @since 3.0.0
     */
    public $signer = '';

    /**
     * @var array<string, array<int, string>> Algorithm types.
     * @since 3.0.0
     */
    public array $algorithmTypes = [
        self::SYMMETRIC => [
            self::HS256,
            self::HS384,
            self::HS512,
        ],
        self::ASYMMETRIC => [
            self::RS256,
            self::RS384,
            self::RS512,
            self::ES256,
            self::ES384,
            self::ES512,
            self::EDDSA,
            self::BLAKE2B,
        ],
    ];

    private ?BaseJwt\Configuration $configuration = null;

    /**
     * @throws InvalidConfigException
     */
    public function init(): void
    {
        parent::init();

        $signerId = $this->signer;
        if ($this->signer instanceof BaseJwt\Signer) {
            $signerId = $this->signer->algorithmId();
        }
        if (\in_array($signerId, $this->algorithmTypes[self::SYMMETRIC], true)) {
            $this->configuration = BaseJwt\Configuration::forSymmetricSigner(
                $this->buildSigner($this->signer),
                $this->buildKey($this->signingKey),
                $this->prepareEncoder(),
                $this->prepareDecoder()
            );
        } elseif (\in_array($signerId, $this->algorithmTypes[self::ASYMMETRIC], true)) {
            $this->configuration = BaseJwt\Configuration::forAsymmetricSigner(
                $this->buildSigner($this->signer),
                $this->buildKey($this->signingKey),
                $this->buildKey($this->verifyingKey),
                $this->prepareEncoder(),
                $this->prepareDecoder()
            );
        } else {
            throw new InvalidConfigException('Invalid signer ID!');
        }
    }

    /**
     * @throws InvalidConfigException
     * @since 3.0.0
     */
    public function getConfiguration(): BaseJwt\Configuration
    {
        if ($this->configuration === null) {
            throw new InvalidConfigException('Configuration has not been set up. Did you call init()?');
        }

        return $this->configuration;
    }

    /**
     * Please note that since 4.0.0 Builder object is immutable.
     * @see https://lcobucci-jwt.readthedocs.io/en/latest/issuing-tokens/ for details of using the builder.
     * @throws InvalidConfigException
     */
    public function getBuilder(?BaseJwt\ClaimsFormatter $claimFormatter = null): BaseJwt\Builder
    {
        return $this->getConfiguration()->builder($claimFormatter);
    }

    /**
     * @see https://lcobucci-jwt.readthedocs.io/en/latest/parsing-tokens/ for details of using the parser.
     * @throws InvalidConfigException
     */
    public function getParser(): BaseJwt\Parser
    {
        return $this->getConfiguration()->parser();
    }

    /**
     * @see https://lcobucci-jwt.readthedocs.io/en/stable/validating-tokens/ for details of using the validator.
     * @throws InvalidConfigException
     */
    public function getValidator(): BaseJwt\Validator
    {
        return $this->getConfiguration()->validator();
    }

    /**
     * This method goes through every single constraint in the set, groups all the violations, and throws an exception
     * with the grouped violations.
     * @param non-empty-string|BaseJwt\Token $jwt JWT string or instance of Token
     * @throws BaseJwt\Validation\RequiredConstraintsViolated When constraint is violated
     * @throws BaseJwt\Validation\NoConstraintsGiven When no constraints are provided
     * @throws InvalidConfigException
     * @since 3.0.0
     */
    public function assert($jwt): void
    {
        $configuration = $this->getConfiguration();
        $token = $jwt instanceof BaseJwt\Token ? $jwt : $this->parse($jwt);
        $constraints = $this->prepareValidationConstraints();
        $configuration->validator()->assert($token, ...$constraints);
    }

    /**
     * This method return false on first constraint violation
     * @param non-empty-string|BaseJwt\Token $jwt JWT string or instance of Token
     * @throws InvalidConfigException
     * @since 3.0.0
     */
    public function validate($jwt): bool
    {
        $configuration = $this->getConfiguration();
        $token = $jwt instanceof BaseJwt\Token ? $jwt : $this->parse($jwt);
        $constraints = $this->prepareValidationConstraints();

        return $configuration->validator()->validate($token, ...$constraints);
    }

    /**
     * @return BaseJwt\Validation\Constraint[]
     * @throws InvalidConfigException
     */
    protected function prepareValidationConstraints(): array
    {
        $configuredConstraints = $this->getConfiguration()->validationConstraints();
        if (!empty($configuredConstraints)) {
            return $configuredConstraints;
        }

        return parent::prepareValidationConstraints();
    }
}
