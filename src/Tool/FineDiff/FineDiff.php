<?php

namespace App\Tool\FineDiff;

/**
 * FINE granularity DIFF
 * Based on https://github.com/gorhill/PHP-FineDiff (MIT)
 * @TODO : Move library to an external repo and require it through composer
 * @TODO : Remove phpmd @suprressWarning and refork to remove the warnings
 */

/**
 * Usage (simplest):
 *
 *   include 'finediff.php';
 *
 *   // for the stock stack, granularity values are:
 *   // self::$paragraphGranularity = paragraph/line level
 *   // self::$sentenceGranularity = sentence level
 *   // self::$wordGranularity = word level
 *   // self::$characterGranularity = character level [default]
 *
 *   $opcodes = self::getDiffOpcodes($fromText, $toText [, $granularityStack = null] );
 *   // store opcodes for later use...
 *
 *   ...
 *
 *   // restore $toText from $fromText + $opcodes
 *   include 'finediff.php';
 *   $toText = self::renderToTextFromOpcodes($fromText, $opcodes);
 *
 *   ...
 */

/**
 * Persisted opcodes (string) are a sequence of atomic opcode.
 * A single opcode can be one of the following:
 *   c | c{n} | d | d{n} | i:{c} | i{length}:{s}
 *   'c'        = copy one character from source
 *   'c{n}'     = copy n characters from source
 *   'd'        = skip one character from source
 *   'd{n}'     = skip n characters from source
 *   'i:{c}     = insert character 'c'
 *   'i{n}:{s}' = insert string s, which is of length n
 *
 * Do not exist as of now, under consideration:
 *   'm{n}:{o}  = move n characters from source o characters ahead.
 *   It would be essentially a shortcut for a delete->copy->insert
 *   command (swap) for when the inserted segment is exactly the same
 *   as the deleted one, and with only a copy operation in between.
 *   TODO: How often this case occurs? Is it worth it? Can only
 *   be done as a postprocessing method (->optimize()?)
 *
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 * @SuppressWarnings(PHPMD.CyclomaticComplexity)
 * @SuppressWarnings(PHPMD.ElseExpression)
 * @SuppressWarnings(PHPMD.NPathComplexity)
 * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
 */
class FineDiff
{
    private $stackPointer;
    private $fromOffset;
    private $lastEdit;
    /**
     * @var array|null
     */
    private $granularityStack;
    /**
     * @var array
     */
    private $edits;
    /**
     * @var string
     */
    private $fromText;

    /**
     * Constructor
     * ...
     * The $granularityStack allows FineDiff to be configurable so that
     * a particular stack tailored to the specific content of a document can
     * be passed.
     * @param string $fromText
     * @param string $toText
     * @param null $granularityStack
     */
    public function __construct($fromText = '', $toText = '', $granularityStack = null)
    {
        // setup stack for generic text documents by default
        $this->granularityStack = $granularityStack ? $granularityStack : self::$characterGranularity;
        $this->edits = array();
        $this->fromText = $fromText;
        $this->doDiff($fromText, $toText);
    }

    public function getOps()
    {
        return $this->edits;
    }

    public function getOpcodes()
    {
        $opcodes = array();
        foreach ($this->edits as $edit) {
            $opcodes[] = $edit->getOpcode();
        }
        return implode('', $opcodes);
    }

    public function renderDiffToHTML($decorators=[['<del>','</del>'],['<ins>','</ins>']])
    {
        $inOffset = 0;
        ob_start();
        foreach ($this->edits as $edit) {
            $n = $edit->getFromLen();
            if ($edit instanceof FineDiffCopyOp) {
                self::renderDiffToHTMLFromOpcode('c', $this->fromText, $inOffset, $n, $decorators);
                continue;
            } elseif ($edit instanceof FineDiffDeleteOp) {
                self::renderDiffToHTMLFromOpcode('d', $this->fromText, $inOffset, $n, $decorators);
                continue;
            } elseif ($edit instanceof FineDiffInsertOp) {
                self::renderDiffToHTMLFromOpcode('i', $edit->getText(), 0, $edit->getToLen(), $decorators);
                continue;
            }
            /* $edit instanceof FineDiffReplaceOp ) */
            self::renderDiffToHTMLFromOpcode('d', $this->fromText, $inOffset, $n, $decorators);
            self::renderDiffToHTMLFromOpcode('i', $edit->getText(), 0, $edit->getToLen(), $decorators);

            $inOffset += $n;
        }
        return ob_get_clean();
    }

    /**------------------------------------------------------------------------
     * Return an opcodes string describing the diff between a "From" and a
     * "To" string
     * @param $from
     * @param $to
     * @param null $granularities
     * @return string
     */
    public static function getDiffOpcodes($fromString, $toString, $granularities = null)
    {
        $diff = new FineDiff($fromString, $toString, $granularities);
        return $diff->getOpcodes();
    }

    /**------------------------------------------------------------------------
     * Return an iterable collection of diff ops from an opcodes string
     * @param $opcodes
     * @return array
     */
    public static function getDiffOpsFromOpcodes($opcodes)
    {
        $diffOps = new FineDiffOps();
        self::renderFromOpcodes(null, $opcodes, array($diffOps, 'appendOpcode'));

        return $diffOps->edits;
    }

    /**------------------------------------------------------------------------
     * Re-create the "To" string from the "From" string and an "Opcodes" string
     * @param $from
     * @param $opcodes
     * @return false|string
     */
    public static function renderToTextFromOpcodes($from, $opcodes)
    {
        ob_start();
        self::renderFromOpcodes($from, $opcodes, array('FineDiff','renderToTextFromOpcode'));
        return ob_get_clean();
    }

    /**------------------------------------------------------------------------
     * Render the diff to an HTML string -- UTF8 unsafe
     * @param $from
     * @param $opcodes
     * @return false|string
     */
    public static function renderDiffToHTMLFromOpcodes($from, $opcodes)
    {
        ob_start();
        self::renderFromOpcodes($from, $opcodes, array('FineDiff','renderDiffToHTMLFromOpcode'));
        return ob_get_clean();
    }

    /**------------------------------------------------------------------------
     * Render the diff to an HTML string -- UTF8 safe
     * @param $from
     * @param $opcodes
     * @return false|string
     */
    public static function renderUTF8DiffToHTMLFromOpcodes($from, $opcodes)
    {
        ob_start();
        self::renderUTF8FromOpcode($from, $opcodes, array('FineDiff','renderDiffToHTMLFromOpcode'));
        return ob_get_clean();
    }

    /**------------------------------------------------------------------------
     * Generic opcodes parser, user must supply callback for handling
     * single opcode
     * @param $from
     * @param $opcodes
     * @param $callback
     */
    public static function renderFromOpcodes($from, $opcodes, $callback)
    {
        if (!is_callable($callback)) {
            return;
        }
        $opcodesLen = strlen($opcodes);
        $fromOffset = $opcodesOffset = 0;
        while ($opcodesOffset <  $opcodesLen) {
            $opcode = substr($opcodes, $opcodesOffset, 1);
            $opcodesOffset++;
            $number = intval(substr($opcodes, $opcodesOffset));
            $opcodesOffset += $number ? strlen(strval($number)) : 0;
            $number = $number ? $number : 1;
            if ($opcode === 'c') { // copy number characters from source
                call_user_func($callback, 'c', $from, $fromOffset, $number, '');
                $fromOffset += $number;
                continue;
            } elseif ($opcode === 'd') { // delete number characters from source
                call_user_func($callback, 'd', $from, $fromOffset, $number, '');
                $fromOffset += $number;
                continue;
            }

            /* $opcode === 'i', so insert number characters from opcodes */
            call_user_func($callback, 'i', $opcodes, $opcodesOffset + 1, $number);
            $opcodesOffset += 1 + $number;
        }
    }

    /**------------------------------------------------------------------------
     * Generic opcodes parser, user must supply callback for handling
     * single opcode
     * @param $from
     * @param $opcodes
     * @param $callback
     */
    private static function renderUTF8FromOpcode($from, $opcodes, $callback)
    {
        if (!is_callable($callback)) {
            return;
        }
        $fromLen = strlen($from);
        $opcodesLen = strlen($opcodes);
        $fromOffset = $opcodesOffset = 0;
        $lastToChars = '';
        while ($opcodesOffset <  $opcodesLen) {
            $opcode = substr($opcodes, $opcodesOffset, 1);
            $opcodesOffset++;
            $number = intval(substr($opcodes, $opcodesOffset));
            $opcodesOffset += $number ? strlen(strval($number)) : 0;
            $number = $number ? $number : 1;
            if ($opcode === 'c' || $opcode === 'd') {
                $beg = $fromOffset;
                $end = $fromOffset + $number;
                while ($beg > 0 && (ord($from[$beg]) & 0xC0) === 0x80) {
                    $beg--;
                }
                while ($end < $fromLen && (ord($from[$end]) & 0xC0) === 0x80) {
                    $end++;
                }
                if ($opcode === 'c') { // copy number characters from source
                    call_user_func($callback, 'c', $from, $beg, $end - $beg, '');
                    $lastToChars = substr($from, $beg, $end - $beg);
                }
                if ( $opcode === 'd' ) { // delete number characters from source
                    call_user_func($callback, 'd', $from, $beg, $end - $beg, '');
                }
                $fromOffset += $number;
            }
            if ( $opcode === 'i' ) { // insert number characters from opcodes
                $opcodesOffset += 1;
                $prefix = '';
                if (strlen($lastToChars) > 0 && (ord($opcodes[$opcodesOffset]) & 0xC0) === 0x80) {
                    $beg = strlen($lastToChars) - 1;
                    while ($beg > 0 && (ord($lastToChars[$beg]) & 0xC0) === 0x80) {
                        $beg--;
                    }
                    $prefix = substr($lastToChars, $beg);
                }
                $end = $fromOffset;
                while ($end < $fromLen && (ord($from[$end]) & 0xC0) === 0x80) {
                    $end++;
                }
                $toInsert = $prefix . substr($opcodes, $opcodesOffset, $number) . substr($from, $end, $end - $fromOffset);
                call_user_func($callback, 'i', $toInsert, 0, strlen($toInsert));
                $opcodesOffset += $number;
                $lastToChars = $toInsert;
            }
        }
    }

    /**
     * Stock granularity stacks and delimiters
     */

    const PARAGRAPH_DELIMITERS = "\n\r";
    public static $paragraphGranularity = array(
        self::PARAGRAPH_DELIMITERS
    );
    const SENTENCE_DELIMITERS = ".\n\r";
    public static $sentenceGranularity = array(
        self::PARAGRAPH_DELIMITERS,
        self::SENTENCE_DELIMITERS
    );
    const WORD_DELIMITERS = " \t.\n\r";
    public static $wordGranularity = array(
        self::PARAGRAPH_DELIMITERS,
        self::SENTENCE_DELIMITERS,
        self::WORD_DELIMITERS
    );
    const CHARACTER_DELIMITERS = "";
    public static $characterGranularity = array(
        self::PARAGRAPH_DELIMITERS,
        self::SENTENCE_DELIMITERS,
        self::WORD_DELIMITERS,
        self::CHARACTER_DELIMITERS
    );

    public static $textStack = array(
        ".",
        " \t.\n\r",
        ""
    );

    /**------------------------------------------------------------------------
     *
     * Private section
     *
     */

    /**
     * Entry point to compute the diff.
     * @param $fromText
     * @param $toText
     */
    private function doDiff($fromText, $toText)
    {
        $this->lastEdit = false;
        $this->stackPointer = 0;
        $this->fromText = $fromText;
        $this->fromOffset = 0;
        // can't diff without at least one granularity specifier
        if (empty($this->granularityStack)) {
            return;
        }
        $this->processGranularity($fromText, $toText);
    }

    /**
     * This is the recursive function which is responsible for
     * handling/increasing granularity.
     *
     * Incrementally increasing the granularity is key to compute the
     * overall diff in a very efficient way.
     * @param $fromSegment
     * @param $toSegment
     */
    private function processGranularity($fromSegment, $toSegment)
    {
        $delimiters = $this->granularityStack[$this->stackPointer++];
        $hasNextStage = $this->stackPointer < count($this->granularityStack);
        foreach (self::doFragmentDiff($fromSegment, $toSegment, $delimiters) as $fragmentEdit) {
            // increase granularity
            if ($fragmentEdit instanceof FineDiffReplaceOp && $hasNextStage) {
                $this->processGranularity(
                    substr($this->fromText, $this->fromOffset, $fragmentEdit->getFromLen()),
                    $fragmentEdit->getText()
                );
            }
            // fuse copy ops whenever possible
            elseif ($fragmentEdit instanceof FineDiffCopyOp && $this->lastEdit instanceof FineDiffCopyOp) {
                $this->edits[count($this->edits)-1]->increase($fragmentEdit->getFromLen());
                $this->fromOffset += $fragmentEdit->getFromLen();
            } else {
                /* $fragmentEdit instanceof FineDiffCopyOp */
                /* $fragmentEdit instanceof FineDiffDeleteOp */
                /* $fragmentEdit instanceof FineDiffInsertOp */
                $this->edits[] = $this->lastEdit = $fragmentEdit;
                $this->fromOffset += $fragmentEdit->getFromLen();
            }
        }
        $this->stackPointer--;
    }

    /**
     * This is the core algorithm which actually perform the diff itself,
     * fragmenting the strings as per specified delimiters.
     *
     * This function is naturally recursive, however for performance purpose
     * a local job queue is used instead of outright recursivity.
     * @param $fromText
     * @param $toText
     * @param $delimiters
     * @return array
     */
    private static function doFragmentDiff($fromText, $toText, $delimiters)
    {
        // Empty delimiter means character-level diffing.
        // In such case, use code path optimized for character-level
        // diffing.
        if (empty($delimiters)) {
            return self::doCharDiff($fromText, $toText);
        }

        $result = array();

        // fragment-level diffing
        $fromTextLen = strlen($fromText);
        $toTextLen = strlen($toText);
        $fromFragments = self::extractFragments($fromText, $delimiters);
        $toFragments = self::extractFragments($toText, $delimiters);

        $jobs = array(array(0, $fromTextLen, 0, $toTextLen));

        $cachedArrayKeys = array();

        while ($job = array_pop($jobs)) {

            // get the segments which must be diff'ed
            list($fromSegmentStart, $fromSegmentEnd, $toSegmentStart, $toSegmentEnd) = $job;

            // catch easy cases first
            $fromSegmentLength = $fromSegmentEnd - $fromSegmentStart;
            $toSegmentLength = $toSegmentEnd - $toSegmentStart;
            if (!$fromSegmentLength || !$toSegmentLength) {
                if ($fromSegmentLength) {
                    $result[$fromSegmentStart * 4] = new FineDiffDeleteOp($fromSegmentLength);
                } elseif ($toSegmentLength) {
                    $result[$fromSegmentStart * 4 + 1] = new FineDiffInsertOp(substr($toText, $toSegmentStart, $toSegmentLength));
                }
                continue;
            }

            // find longest copy operation for the current segments
            $bestCopyLength = 0;

            $fromBaseFragmentIdx = $fromSegmentStart;

            /** @var array $cacheKeys cached array keys for current segment */
            $currentSegmentKeys = array();

            while ($fromBaseFragmentIdx < $fromSegmentEnd) {
                $fromBaseFragment = $fromFragments[$fromBaseFragmentIdx];
                $fromBaseFragmentLen = strlen($fromBaseFragment);
                // performance boost: cache array keys
                if (!isset($currentSegmentKeys[$fromBaseFragment])) {
                    if (!isset($cachedArrayKeys[$fromBaseFragment])) {
                        $toAllFragmentIndices = $cachedArrayKeys[$fromBaseFragment] = array_keys($toFragments, $fromBaseFragment, true);
                    } else {
                        $toAllFragmentIndices = $cachedArrayKeys[$fromBaseFragment];
                    }
                    // get only indices which falls within current segment
                    if ($toSegmentStart > 0 || $toSegmentEnd < $toTextLen) {
                        $toFragmentIndices = array();
                        foreach ($toAllFragmentIndices as $toFragmentIndex) {
                            if ($toFragmentIndex < $toSegmentStart) {
                                continue;
                            }
                            if ($toFragmentIndex >= $toSegmentEnd) {
                                break;
                            }
                            $toFragmentIndices[] = $toFragmentIndex;
                        }
                        $currentSegmentKeys[$fromBaseFragment] = $toFragmentIndices;
                    } else {
                        $toFragmentIndices = $toAllFragmentIndices;
                    }
                } else {
                    $toFragmentIndices = $currentSegmentKeys[$fromBaseFragment];
                }
                // iterate through collected indices
                foreach ($toFragmentIndices as $toBaseFragmentIndex) {
                    $fragmentIndexOffset = $fromBaseFragmentLen;
                    // iterate until no more match
                    for (; ;) {
                        $fragmentFromIndex = $fromBaseFragmentIdx + $fragmentIndexOffset;
                        if ($fragmentFromIndex >= $fromSegmentEnd) {
                            break;
                        }
                        $fragmentToIndex = $toBaseFragmentIndex + $fragmentIndexOffset;
                        if ($fragmentToIndex >= $toSegmentEnd) {
                            break;
                        }
                        if ($fromFragments[$fragmentFromIndex] !== $toFragments[$fragmentToIndex]) {
                            break;
                        }
                        $fragmentLength = strlen($fromFragments[$fragmentFromIndex]);
                        $fragmentIndexOffset += $fragmentLength;
                    }
                    if ($fragmentIndexOffset > $bestCopyLength) {
                        $bestCopyLength = $fragmentIndexOffset;
                        $bestFromStart = $fromBaseFragmentIdx;
                        $bestToStart = $toBaseFragmentIndex;
                    }
                }

                $fromBaseFragmentIdx += strlen($fromBaseFragment);
                // If match is larger than half segment size, no point trying to find better
                // TODO: Really?
                if ($bestCopyLength >= $fromSegmentLength / 2) {
                    break;
                }
                // no point to keep looking if what is left is less than
                // current best match
                if ($fromBaseFragmentIdx + $bestCopyLength >= $fromSegmentEnd) {
                    break;
                }
            }

            if ($bestCopyLength) {
                $jobs[] = array($fromSegmentStart, $bestFromStart, $toSegmentStart, $bestToStart);
                $result[$bestFromStart * 4 + 2] = new FineDiffCopyOp($bestCopyLength);
                $jobs[] = array($bestFromStart + $bestCopyLength, $fromSegmentEnd, $bestToStart + $bestCopyLength, $toSegmentEnd);
            } else {
                $result[$fromSegmentStart * 4 ] = new FineDiffReplaceOp($fromSegmentLength, substr($toText, $toSegmentStart, $toSegmentLength));
            }
        }

        ksort($result, SORT_NUMERIC);

        return array_values($result);
    }

    /**
     * Perform a character-level diff.
     *
     * The algorithm is quite similar to doFragmentDiff(), except that
     * the code path is optimized for character-level diff -- strpos() is
     * used to find out the longest common subequence of characters.
     *
     * We try to find a match using the longest possible subsequence, which
     * is at most the length of the shortest of the two strings, then incrementally
     * reduce the size until a match is found.
     *
     * I still need to study more the performance of this function. It
     * appears that for long strings, the generic doFragmentDiff() is more
     * performant. For word-sized strings, doCharDiff() is somewhat more
     * performant.
     * @param $fromText
     * @param $toText
     * @return array
     */
    private static function doCharDiff($fromText, $toText)
    {
        $result = array();
        $jobs = array(array(0, strlen($fromText), 0, strlen($toText)));
        while ($job = array_pop($jobs)) {
            // get the segments which must be diff'ed
            list($fromSegmentStart, $fromSegmentEnd, $toSegmentStart, $toSegmentEnd) = $job;
            $fromSegmentLen = $fromSegmentEnd - $fromSegmentStart;
            $toSegmentLen = $toSegmentEnd - $toSegmentStart;

            // catch easy cases first
            if (!$fromSegmentLen || !$toSegmentLen) {
                if ($fromSegmentLen) {
                    $result[$fromSegmentStart * 4 + 0] = new FineDiffDeleteOp($fromSegmentLen);
                } elseif ($toSegmentLen) {
                    $result[$fromSegmentStart * 4 + 1] = new FineDiffInsertOp(substr($toText, $toSegmentStart, $toSegmentLen));
                }
                continue;
            }
            if ($fromSegmentLen >= $toSegmentLen) {
                $copyLen = $toSegmentLen;
                while ($copyLen) {
                    $toCopyStart = $toSegmentStart;
                    $toCopyStartMax = $toSegmentEnd - $copyLen;
                    while ($toCopyStart <= $toCopyStartMax) {
                        $fromCopyStart = strpos(substr($fromText, $fromSegmentStart, $fromSegmentLen), substr($toText, $toCopyStart, $copyLen));
                        if ($fromCopyStart !== false) {
                            $fromCopyStart += $fromSegmentStart;
                            break 2;
                        }
                        $toCopyStart++;
                    }
                    $copyLen--;
                }
            } else {
                $copyLen = $fromSegmentLen;
                while ($copyLen) {
                    $fromCopyStart = $fromSegmentStart;
                    $fromCopyStartMax = $fromSegmentEnd - $copyLen;
                    while ($fromCopyStart <= $fromCopyStartMax) {
                        $toCopyStart = strpos(substr($toText, $toSegmentStart, $toSegmentLen), substr($fromText, $fromCopyStart, $copyLen));
                        if ($toCopyStart !== false) {
                            $toCopyStart += $toSegmentStart;
                            break 2;
                        }
                        $fromCopyStart++;
                    }
                    $copyLen--;
                }
            }
            // match found
            if ($copyLen) {
                $jobs[] = array($fromSegmentStart, $fromCopyStart, $toSegmentStart, $toCopyStart);
                $result[$fromCopyStart * 4 + 2] = new FineDiffCopyOp($copyLen);
                $jobs[] = array($fromCopyStart + $copyLen, $fromSegmentEnd, $toCopyStart + $copyLen, $toSegmentEnd);
            }
            // no match,  so delete all, insert all
            else {
                $result[$fromSegmentStart * 4] = new FineDiffReplaceOp($fromSegmentLen, substr($toText, $toSegmentStart, $toSegmentLen));
            }
        }
        ksort($result, SORT_NUMERIC);
        return array_values($result);
    }

    /**
     * Efficiently fragment the text into an array according to
     * specified delimiters.
     * No delimiters means fragment into single character.
     * The array indices are the offset of the fragments into
     * the input string.
     * A sentinel empty fragment is always added at the end.
     * Careful: No check is performed as to the validity of the
     * delimiters.
     * @param $text
     * @param $delimiters
     * @return array
     */
    private static function extractFragments($text, $delimiters)
    {
        // special case: split into characters
        if (empty($delimiters)) {
            $chars = str_split($text, 1);
            $chars[strlen($text)] = '';
            return $chars;
        }
        $fragments = array();
        $start = $end = 0;
        for (;;) {
            $end += strcspn($text, $delimiters, $end);
            $end += strspn($text, $delimiters, $end);
            if ($end === $start) {
                break;
            }
            $fragments[$start] = substr($text, $start, $end - $start);
            $start = $end;
        }
        $fragments[$start] = '';
        return $fragments;
    }

    private static function renderDiffToHTMLFromOpcode(
        $opcode,
        $from,
        $fromOffset,
        $fromLen,
        array $decorators
    ) {
        if ($opcode === 'c') {
            echo htmlspecialchars(substr($from, $fromOffset, $fromLen));
            return;
        } elseif ($opcode === 'd') {
            $deletion = substr($from, $fromOffset, $fromLen);
            if (strcspn($deletion, " \n\r") === 0) {
                $deletion = str_replace(array("\n","\r"), array('\n','\r'), $deletion);
            }
            echo $decorators[0][0], htmlspecialchars($deletion), $decorators[0][1];
            return;
        }

        /* $opcode === 'i' */
        echo $decorators[1][0], htmlspecialchars(substr($from, $fromOffset, $fromLen)), $decorators[1][1];
    }
}
