<?php

declare(strict_types=1);

/*
 * This file is part of the box project.
 *
 * (c) Kevin Herrera <kevin@herrera.io>
 *     Théo Fidry <theo.fidry@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace KevinGH\Box\Annotation;

use function array_filter;
use Assert\Assertion;
use Hoa\Compiler\Llk\TreeNode;
use InvalidArgumentException;
use function array_map;
use function array_shift;
use function array_values;
use function implode;
use function in_array;
use function sprintf;
use function strtolower;
use function trim;

/**
 * @private
 */
final class AnnotationDumper
{
    /**
     * Dumps the list of annotations from the given tree.
     *
     * @param string[] $ignored List of annotations to ignore
     *
     * @throws InvalidToken
     *
     * @return string[]
     */
    public function dump(TreeNode $node, array $ignored): array
    {
        Assertion::allString($ignored);

        $ignored = array_map('strtolower', $ignored);

        if ('#annotations' !== $node->getId()) {
            return [];
        }

        return array_values(
            array_filter(
                $this->transformNodesToString(
                    $node->getChildren(),
                    $ignored
                )
            )
        );
    }

    /**
     * @param TreeNode $nodes
     * @param string[] $ignored
     *
     * @return (string|null)[]
     */
    private function transformNodesToString(array $nodes, array $ignored): array
    {
        $ignored = array_values(
            array_filter(
                array_map(
                    function (string $ignored): ?string {
                        return strtolower(trim($ignored));
                    },
                    $ignored
                )
            )
        );

        return array_map(
            function (TreeNode $node) use ($ignored): ?string {
                return $this->transformNodeToString($node, $ignored);
            },
            $nodes
        );
    }

    /**
     * @param string[] $ignored
     */
    private function transformNodeToString(TreeNode $node, array $ignored): ?string
    {
        if ('token' === $node->getId()) {
            if (in_array($node->getValueToken(), ['identifier', 'simple_identifier', 'integer', 'float', 'boolean', 'identifier_ns'], true)) {
                return $node->getValueValue();
            }

            if ('string' === $node->getValueToken()) {
                return sprintf('"%s"', $node->getValueValue());
            }

            if ('valued_identifier' === $node->getValueToken()) {
                return sprintf('%s()', $node->getValueValue());
            }

            throw InvalidToken::createForUnknownType($node);
        }

        if ('#parameters' === $node->getId()) {
            $transformedChildren = $this->transformNodesToString(
                $node->getChildren(),
                $ignored
            );

            return implode(',', $transformedChildren);
        }

        if ('#named_parameter' === $node->getId() || '#pair' === $node->getId()) {
            Assertion::same($node->getChildrenNumber(), 2);

            $name = $node->getChild(0);
            $parameter = $node->getChild(1);

            return sprintf(
                '%s=%s',
                $this->transformNodeToString($name, $ignored),
                $this->transformNodeToString($parameter, $ignored)
            );
        }

        if ('#value' === $node->getId()) {
            Assertion::same($node->getChildrenNumber(), 1);

            return $this->transformNodeToString($node->getChild(0), $ignored);
        }

        if ('#string' === $node->getId()) {
            Assertion::lessOrEqualThan($node->getChildrenNumber(), 1);

            return 1 === $node->getChildrenNumber() ? $this->transformNodeToString($node->getChild(0), $ignored) : '""';
        }

        if ('#list' === $node->getId() || '#map' === $node->getId()) {
            $transformedChildren = $this->transformNodesToString(
                $node->getChildren(),
                $ignored
            );

            return sprintf(
                '{%s}',
                implode(
                    ',',
                    $transformedChildren
                )
            );
        }

        if ('#annotation' === $node->getId()) {
            Assertion::greaterOrEqualThan($node->getChildrenNumber(), 1);

            $children = $node->getChildren();

            /** @var TreeNode $token */
            $token = array_shift($children);
            $parameters = array_values($children);

            if ('simple_identifier' === $token->getValueToken()) {
                Assertion::count($parameters, 0);

                $tokenValue = $token->getValueValue();

                return in_array(strtolower($tokenValue), $ignored, true) ? null : '@' . $tokenValue;
            }

            if ('valued_identifier' === $token->getValueToken()) {
                $transformedChildren = $this->transformNodesToString(
                    $parameters,
                    $ignored
                );

                return sprintf(
                    '@%s(%s)',
                    $token->getValueValue(),
                    implode(
                        '',
                        $transformedChildren
                    )
                );
            }
        }

        if ('#unnamed_parameter' === $node->getId()) {
            Assertion::same($node->getChildrenNumber(), 1);

            return $this->transformNodeToString($node->getChild(0), $ignored);
        }

        if ('#reference' === $node->getId()) {
            Assertion::same($node->getChildrenNumber(), 1);

            return $this->transformNodeToString($node->getChild(0), $ignored);
        }

        if ('#constant' === $node->getId()) {
            Assertion::same($node->getChildrenNumber(), 2);

            return sprintf(
                '%s::%s',
                $this->transformNodeToString($node->getChild(0), $ignored),
                $this->transformNodeToString($node->getChild(1), $ignored)
            );
        }

        $x = '';
    }
}