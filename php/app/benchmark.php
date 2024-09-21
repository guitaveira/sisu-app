<?php


use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Rsa\Sha256 as RsaSha256;
use Lcobucci\JWT\Signer\Ecdsa\Sha256 as EcdsaSha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

function benchmark_verification($config, $token, $iterations = 1000)
{
    $start = microtime(true);

    for ($i = 0; $i < $iterations; $i++) {
        $config->validator()->assert($token, new SignedWith($config->signer(), $config->signingKey()));
    }

    $end = microtime(true);
    $duration = $end - $start;
    $averageTime = $duration / $iterations;

    return [
        'total_time' => $duration,
        'average_time' => $averageTime,
    ];
}

// Configuration for HS256
$hs256Config = Configuration::forSymmetricSigner(
    new Sha256(),
    InMemory::base64Encoded('G2vq3pkxRyZU8slciwMZH/ks3xg5cmUH2YFC72vNg48=') // Replace with your secret
);

// Create a token for HS256
$hs256Token = $hs256Config->builder()
    ->issuedBy('https://example.com')
    ->permittedFor('https://client.com')
    ->identifiedBy('4f1g23a12aa', true)
    ->issuedAt(new \DateTimeImmutable())
    ->canOnlyBeUsedAfter(new \DateTimeImmutable())
    ->expiresAt((new \DateTimeImmutable())->modify('+1 hour'))
    ->withClaim('uid', 1)
    ->getToken($hs256Config->signer(), $hs256Config->signingKey());

// Configuration for RS256
$rsaPrivateKey = '-----BEGIN RSA PRIVATE KEY-----
MIIJKwIBAAKCAgEAwCoerd4wrOJWZ0ZRwh2lSfV1luxglhEg9TXo0VsjR71bTvGV
+A1ZnBA0z0LP40PgrTNWFfcjwOpKq1Qit12MYc30kARuU4MGLTtjZgvZeWbO4E4+
678xcidL/gIFU4g1i4/v4U4SIFnUouqQTBmttghZC9CCwJ3jc2jlz7VzUISb8lgk
8B7+8lPEjOhLF7fVBySSvmu3uRZS2+ac1gxRo+4PowEknM/JY2CqZLydp0vcacMo
yvaZwwxSS/WPAKRNb1P5IdrjtbUUcPpSNH+RcBPmS7OIi7Pw9GS/oJMjhggNf/eg
0iFaiAP2z+11bRQ2nX2Yk6qt9WDIymfKZlx78Ep6IKbkuFOx4BDchvBLT/uzh30Q
Bx4EFeLfOwJvYB8c5ZdXiRvh9YncAtK858VK5GzpMgzDXC093fWTjMkxcCt5ZZyR
cAlt1G9NIOnR8l1D3WXT4dn+YBreudRjM67WYkmJ58PKPO8H82Gw8m6N3VsKHVIv
RrYWLGwQOAKgDxOwJP42TGXGDzpGU6ED/adsb+mN8qjRm9Hj0EA0cFZiWvUOnGTp
MzwP2A2+TqT8Wc+ILkIzoD78HfSUR2P8sHbMmvPBT9ht3OodRnP0saivuVeQ2PH9
nA08RKl9F1Gyn0Qjhu/3WKjJdRDVKHwkvgWNJFPhZ3hl0Cf1hOvEmKH+KykCAwEA
AQKCAgEAkXAPciYlDuPq4xT8kf8f9z7YdZaHb2ydVhksES96HzS4Y6JCj8+Cz7QQ
VAFMF8Rqyot9DvjSTZLFWrA96ivaMLfQ7iL8YSZcSWWWUEiNmu1ti6SMyJ4WzT/i
qudaoqMHa45PzmTpIST74yXGemJA7/GXe3KfUyrsV4+/xxmcogcLhDqkEjxTVpKB
wueY1eWjTFmo2ofqMCIuKhJ7ByGhtIFbwlH+JNS6pgUmUUHTzCeFNWKogBxtuYqc
yrKaPbEcjjKu7qmdCAx54RwDlYorR/k3pnnF0X4p0r5hriVOkIWNuhlv1Tm7LBBb
/3jIE/tlboL9NF3MdVeAAHjXXeuHPL77vU8QIbxT2raYr5AURKV0Bcufsu3ayX2W
KYJY4TywxYnIfIGfmcXaa6Da/3+JslAoo9UMwAesQ5hRfiat6oHLIT1MXCQePZD4
IoQc4CjfYIHzc943ZF22EUPN7Em7o26gsDyDYa+RIS3rkBxnJ8yehmXV/s4lHIyu
G+DJDXxKQsQDVWRbhpoToZRqeVl5GAQ672JGnd9rty67PhbU5387YeMQU0iRw/iM
cxo1vn+IKl9sKIGAeKN36lj0s58tyJtd3P4/LXo80Yn5b4qa4nDpOP1EBoO9mkD7
MpmNytq1VseSqNAGFodrw4DLmU8FQRrpaJ+cy9YRQxUTZtW82wECggEBAPmis0C6
wMIgrIVwJPO0yrT+F+I3vHljjXLr2x+4M26+haNtvzHgWbNFyrCL5H7GRf3ETMWc
WsgcvpfLGYCOs+I/j5x8wfWgTzL46DAmiikQNyyFO6ecjQ2VUug8gF5H9QP2lh2c
SfQHoTAOAez9jW4KEGl5JE21Pn+rEhZC4Yjfz8TP8UVmQzjpNlFQYPMFZTsQqBRl
JNlDlOt1NBVt7qc0jnuFuJVxk0YJaP8A7o9fBloqYsBfwds9kvZ7/pt8XAI4NIG/
k81eilPuT+ZQ8vnNCnio1RPijSQDRuP2NPDkOLUhTJbeXjmYb40H9ckCe12tMnU6
yP8lpMq7I+vwYckCggEBAMUQUp+1eZeOaK/hQx0ZYkeWfPq0ykLIZBnCutlQo7FQ
J5GyG70s+JJz9kUuWlt9+ionoD0EmGy1q5+s6GaGFbSAyAwjrmkNplqZ0/eh+zdo
xCrYhVpMIlUU7aqNMNjJR2xltIATkGrlj8bjlUrqa5np607O2mni2S0wvmVGg4P4
rCXC0Qe63xP1UdTRkvNIu5MNhHtm+kWGSIqXavECDqYFXHplbE73LswHj3f4jlW/
H/3DDwuvz8GdxnjPhMDysgBEsYNYRp4GrYPNFbrH9FaH1tfGj9frvLiJzO+RmzmP
Hu2SJlY/bjOYWW217KKYlZCs80TiH7eQk/QPUxsfLmECggEBANT1Oz3pEy+IeCSN
erh8bsDgUrelHJ/hkXWMRy5UEWxUE+VLZmPCJEOPMk5RyOdtdZ/6qhOaQseb3evY
UzUch9BmsLiqpTxJOcceF9WbyxkkwCy2rCFcp+gCjuuXUVscv6RV49H21g/bwmIg
UPw/gTtyUnXn5lR0XZDD+3YKMCR36eLYEddGWepe6PuNOmeXHri4iOp9LmY6BPyo
y3nMgl8ZssMlXEYA0cZZmLyRqvGb+utIZV3/Un0Zlhm3xYgXGta54/Eb4Za9I/xd
vMOaIu1/QYOVY9DG3+js8rjd/GPUDZxXf+LkaDVyGReSxtZny54qdnUTZQxkrKRV
6VsJgiECggEBALwDUcE8hFDbtvevBLg7oq/IXV9Yo+zJge+uAVUbAcJHRilUc/Cu
ek5IQvtIOT83Vzlm6xOsUbzOK3tBnc1LOmQnxjUGyf1C36drQnft3F/GHfr+72Py
ZYMlX4esA6Ghj/pUorzbbZr/gIhyU9rRA24qZq2e33XM0AW0jsLTXuDHnX69e29T
lEhXcwaIGRryFrw7Vl3iJv+0GXvY8VgV7WHqlYvVPlusq8JPqEr/ItWebuhOdQli
aOZCILzcyLzKEJf+8hntXBqjJmMshQHaij0QhyMBN/X63Oh32MXs9tsYuJpTKS56
gCrLvO7Wdnm++Fu7FrJux3H8h5yADns+6aECggEBAJYnpAzZ0I/77wSfraMX/hrZ
ZPuqlmgxXBUGqIYEWklhhTw7QiqyBXztPuNdy7gjKUDsOSEpDPa7F8K9jsxVm5bW
y0ZBqnu6RuUEvD+d3JMgpZxx1JLyMmK7OlIlfhk93OuAKS383FIcbTiYK9tKfvEa
O43TFhTAMZjglWenT8Cxey3nwqlaUnPjkPaqfFy/ffcMCOf8eAUmBp82JGO3osYc
NIYDVwdpDN5hkYpyehsDeLDiX1eTfCE1ZcZFuMcHHlWRSiOmxpZH1RnQFe8frNRS
cOJPB1eW2ny/UXZfeLwheuQfkr5grlke4Z0JiNd86CJ9NOnNIbMDl2PSj7cjMDQ=
-----END RSA PRIVATE KEY-----
';  // Make sure to use the actual path to your RSA private key
$rsaPublicKey = '-----BEGIN PUBLIC KEY-----
MIICIjANBgkqhkiG9w0BAQEFAAOCAg8AMIICCgKCAgEAwCoerd4wrOJWZ0ZRwh2l
SfV1luxglhEg9TXo0VsjR71bTvGV+A1ZnBA0z0LP40PgrTNWFfcjwOpKq1Qit12M
Yc30kARuU4MGLTtjZgvZeWbO4E4+678xcidL/gIFU4g1i4/v4U4SIFnUouqQTBmt
tghZC9CCwJ3jc2jlz7VzUISb8lgk8B7+8lPEjOhLF7fVBySSvmu3uRZS2+ac1gxR
o+4PowEknM/JY2CqZLydp0vcacMoyvaZwwxSS/WPAKRNb1P5IdrjtbUUcPpSNH+R
cBPmS7OIi7Pw9GS/oJMjhggNf/eg0iFaiAP2z+11bRQ2nX2Yk6qt9WDIymfKZlx7
8Ep6IKbkuFOx4BDchvBLT/uzh30QBx4EFeLfOwJvYB8c5ZdXiRvh9YncAtK858VK
5GzpMgzDXC093fWTjMkxcCt5ZZyRcAlt1G9NIOnR8l1D3WXT4dn+YBreudRjM67W
YkmJ58PKPO8H82Gw8m6N3VsKHVIvRrYWLGwQOAKgDxOwJP42TGXGDzpGU6ED/ads
b+mN8qjRm9Hj0EA0cFZiWvUOnGTpMzwP2A2+TqT8Wc+ILkIzoD78HfSUR2P8sHbM
mvPBT9ht3OodRnP0saivuVeQ2PH9nA08RKl9F1Gyn0Qjhu/3WKjJdRDVKHwkvgWN
JFPhZ3hl0Cf1hOvEmKH+KykCAwEAAQ==
-----END PUBLIC KEY-----';    // Make sure to use the path to your RSA public key
$rsaConfig = Configuration::forAsymmetricSigner(
    new RsaSha256(),
    InMemory::plainText($rsaPrivateKey), // Private key for signing
    InMemory::plainText($rsaPublicKey)   // Public key for verification
);

// Create a token for RS256
$rs256Token = $rsaConfig->builder()
    ->issuedBy('https://example.com')
    ->permittedFor('https://client.com')
    ->identifiedBy('4f1g23a12aa', true)
    ->issuedAt(new \DateTimeImmutable())
    ->canOnlyBeUsedAfter(new \DateTimeImmutable())
    ->expiresAt((new \DateTimeImmutable())->modify('+1 hour'))
    ->withClaim('uid', 1)
    ->getToken($rsaConfig->signer(), $rsaConfig->signingKey());

// Configuration for ES256
$ecdsaPrivateKey = 'keys/es256.key';  // Path to your EC private key
$ecdsaPublicKey = 'keys/es256.key.pub';    // Path to your EC public key
$ecdsaConfig = Configuration::forAsymmetricSigner(
    new EcdsaSha256(),
    InMemory::file($ecdsaPrivateKey), // Private key for signing
    InMemory::file($ecdsaPublicKey)   // Public key for verification
);

// Create a token for ES256
$es256Token = $ecdsaConfig->builder()
    ->issuedBy('https://example.com')
    ->permittedFor('https://client.com')
    ->identifiedBy('4f1g23a12aa', true)
    ->issuedAt(new \DateTimeImmutable())
    ->canOnlyBeUsedAfter(new \DateTimeImmutable())
    ->expiresAt((new \DateTimeImmutable())->modify('+1 hour'))
    ->withClaim('uid', 1)
    ->getToken($ecdsaConfig->signer(), $ecdsaConfig->signingKey());

// Benchmarking results
$iterations = 1000;
$results = [];

$results['HS256'] = benchmark_verification($hs256Config, $hs256Token, $iterations);
$results['RS256'] = benchmark_verification($rsaConfig, $rs256Token, $iterations);
$results['ES256'] = benchmark_verification($ecdsaConfig, $es256Token, $iterations);

// Print results
foreach ($results as $algorithm => $result) {
    echo "Algorithm: $algorithm\n";
    echo "Total Time: {$result['total_time']} seconds\n";
    echo "Average Time per Verification: {$result['average_time']} seconds\n\n";
}
