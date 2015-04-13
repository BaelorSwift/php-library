<?php namespace Duffleman\baelor\Results;

use Duffleman\baelor\MagicMethod;

class ResultParser {

    private static $namespace = 'Duffleman\baelor\Results\\';

    public static function build($resultSet, MagicMethod $lastMethod = null)
    {
        $resultSet = self::convertJsonToObject($resultSet);
        $type = self::tryAndGetResultType($resultSet, $lastMethod);

        $collection = self::isCollection($resultSet);

        if ($collection) {
            return new CollectionSet($resultSet, $lastMethod->getName());
        }

        if (empty($type)) {
            dd($resultSet);
        }

        $className = self::$namespace . $type;

        return new $className($resultSet);
    }

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

    private static function convertJsonToObject($resultSet)
    {
        if ( !is_object($resultSet)) {
            $resultSet = json_decode($resultSet);

            return $resultSet;
        }

        return $resultSet;
    }

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

    private static function isCollection($resultSet)
    {
        if (isset($resultSet->result) && is_array($resultSet->result)) {
            return true;
        }

        return false;
    }
}