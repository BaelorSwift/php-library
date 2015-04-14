<?php namespace Duffleman\baelor\Results;

use Duffleman\baelor\MagicMethod;

/**
 * When given a result set, it tries to figure out what it is and builds it up.
 *
 * Class ResultParser
 * @package Duffleman\baelor\Results
 */
class ResultParser {

    /**
     * Namespace of the result directory.
     *
     * @var string
     */
    private static $namespace = 'Duffleman\baelor\Results\\';

    /**
     * Juicy build method.
     * Turns a stdClass from json_decode to an item or collection set.
     *
     * @param                               $resultSet
     * @param \Duffleman\baelor\MagicMethod $lastMethod
     * @return \Duffleman\baelor\Results\CollectionSet
     */
    public static function build($resultSet, MagicMethod $lastMethod = null)
    {
        $resultSet = self::convertJsonToObject($resultSet);
        $type = self::tryAndGetResultType($resultSet, $lastMethod);

        // If it's a collection, return a collection set.
        $collection = self::isCollection($resultSet);

        if ($collection) {
            return new CollectionSet($resultSet, $lastMethod->getName());
        }
        // Otherwise, just return an individual item.

        // If it cannot find the type, just return it as a generic.
        if (empty($type)) {
            $className = self::$namespace . 'Generic';
        } else {
            $className = self::$namespace . $type;
        }

        return new $className($resultSet);
    }

    /**
     * Try to determine the type of the set.
     * If at all possible.
     *
     * @param $resultSet
     * @return string
     */
    private static function determineType($resultSet)
    {
        if (isset($resultSet->result->username)) {
            return 'User';
        }

        if (isset($resultSet->has_lyrics)) {
            return 'Song';
        }

        if (isset($resultSet->album_cover)) {
            return 'Album';
        }

        return 'Generic';
    }

    /**
     * Is it a string, is it an object? No it's a plane.
     * Convert it if needed, otherwise do nothing :P
     *
     * @param $resultSet
     * @return mixed
     */
    private static function convertJsonToObject($resultSet)
    {
        if ( !is_object($resultSet)) {
            $resultSet = json_decode($resultSet);
        }

        return $resultSet;
    }

    /**
     * Determines the type based on the method call. $api->getAlbums for example.
     * Otherwise, return Generic.
     *
     * @param $resultSet
     * @param $lastMethod
     * @return string
     */
    private static function tryAndGetResultType($resultSet, $lastMethod)
    {
        if (is_null($lastMethod)) {
            $type = self::determineType($resultSet);

            return $type;
        } else {
            $type = $lastMethod->getClass();
        }

        return $type;
    }

    /**
     * Determines if the result set is worth sticking into a collection.
     *
     * @param $resultSet
     * @return bool
     */
    private static function isCollection($resultSet)
    {
        if (isset($resultSet->result) && is_array($resultSet->result)) {
            return true;
        }

        return false;
    }
}