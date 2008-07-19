<?php
/**
 * @package  wsdl.writer
 *
 * @version   $$
 * @author    David Giffin <david@giffin.org>
 * @since     PHP 5.0.2
 * @copyright Copyright (c) 2000-2005 David Giffin : LGPL - See LICENCE
 *
 */


/**
 * WSDL Generator for PHP5
 *
 * @package   wsdl.writer
 * @author    David Giffin <david@giffin.org>
 *
 */
class DocBlockParser
{

    private static $docBlockTags = array(
                "@param" => array(
                                "regex"   => "|@param\s+([a-zA-Z0-9\[\]]+)\s+\\\$([a-zA-Z0-9]+)\s+(.*)|",
                                "matches" => array("type", "name", "desc")
                                ),
                "@return" => array(
                                "regex"   => "|@return\s+([a-zA-Z0-9\[\]]+)\s+(.*)|",
                                "matches" => array("type", "desc")
                                ),
                "@author" => array(
                                "regex"   => "|@author\s+(.*)|",
                                "matches" => array("name")
                                ),
                "@internal" => array(
                                "regex"   => "|@internal\s+(.*)|",
                                "matches" => array("usage")
                                ),
                );

    /**
     * Get the Type and Description Information for a Property
     *
     * @param string $name The name of the Property
     * @param string $className the name file which contains
     *                the Class that the property is defined
     */
    public static function getPropertyInfo(ReflectionProperty $property)
    {
        $info        = array();
        $commentLine = $property->getDocComment();

        if (strlen($commentLine)) {
            if (preg_match("|@var\s+([a-zA-Z0-9\[\]]+)([^(\*/)]*)|", $commentLine, $matches)) {
                $info['type'] = $matches[1];
                if (isset($matches[2])) {
                    $info['desc'] = $matches[2];
                }
                return $info;
            }
        }
        return null;
    }

    /**
     * Get the Description from the Doc Block
     *
     * @param  string $doc The Doc Block Text
     * @return string The Description
     */
    public static function getDescription($doc)
    {
        $tagRegex = self::getTagRegex();
        $lines    = split("\n", self::stripCommentChars($doc));
        $desc     = "";

        foreach ($lines as $line) {
            if (preg_match($tagRegex, $line)) {
                return $desc;
            }
            $desc .= $line;
        }
        return $desc;
    }


    /**
     * Get the Tag Information from the Doc Block
     *
     * @param  string $doc The Doc Block Text
     * @return array The list for all of the Tag Information
     */
    public static function getTagInfo($doc)
    {
        $tagRegex     = self::getTagRegex();
        $params       = array();
        $param        = array();
        $inParamBlock = false;
        $wrapped      = null;
        $currentTag   = null;

        // Do a Line at a time
        $lines = split("\n", self::stripCommentChars($doc));

        for ($i = 0, $c = count($lines); $i < $c; $i++) {

            // Loop through the Doc Tag list and Find Matches
            foreach (self::$docBlockTags as $tag => $tagInfo) {
                if (preg_match($tagInfo['regex'], $lines[$i], $matches)) {

                    // Name the matches...
                    $matchNames = $tagInfo['matches'];
                    for ($j = 0; $j < count($matchNames); $j++) {
                        $param[$matchNames[$j]] = $matches[($j + 1)];
                    }

                    // Get the place to put the wrapped lines..
                    $paramKeys        = array_keys($param);
                    $wrapped          = end($paramKeys);
                    $param[$wrapped] .= "\n";
                    $inParamBlock     = true;
                    $currentTag       = $tag;
                    break;
                }
            }

            // Handle Wrapped Lines
            if ($inParamBlock) {
                for ($i++; (isset($lines[$i]) && (preg_match($tagRegex, $lines[$i]) == false)); $i++) {
                    if ($i >= $c) {
                        break;
                    }
                    $param[$wrapped] .= $lines[$i] . "\n";
                }

                $params[]     = array($currentTag => $param);

                // Reset Stuff for Next Param
                $param        = array();
                $inParamBlock = false;
                $i--;
            }
        }

        return $params;
    }


    /**
     * Strip the Comment Chars from the Doc Block
     *
     * @param  string $doc The Doc Block Text
     * @return string The contents of the Doc Block minus the Comment Chars
     */
    private static function stripCommentChars($doc)
    {
        $out = "";
        $lines = split("\n", $doc);
        foreach ($lines as $line) {
            $line = preg_replace("|^\s*/\**|", "", $line);
            $line = preg_replace("|^\s*\**|",  "", $line);
            $line = preg_replace("|^\s*\**/|", "", $line);
            $out .= $line . "\n";
        }
        return $out;
    }

    /**
     * Create a Simple Regex for all Doc Block Tags
     *
     * @return string The regex pattern
     */
    private static function getTagRegex()
    {
        return "/(" . join("|", array_keys(self::$docBlockTags)) . ")/";
    }

}

?>
