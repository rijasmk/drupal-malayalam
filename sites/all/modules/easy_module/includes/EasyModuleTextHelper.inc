<?php
/**
 * @file
 * Helper class for formatting text.
 */

/**
 * Helper class for formatting text.
 *
 * @author rpc
 */
class EasyModuleTextHelper {

  /**
   * Characters for breaking the text.
   */
  static $breakChars = array('.' => 0, '!' => 0, '?' => 0, ' ' => 1);

  /**
   * Format the given string as PHP documentation.
   *
   * @param string $text
   *   text to be formatted
   * @param int $spaces_number
   *   number of spaces after * per line.
   *
   * @return string
   *   formatted string
   */
  public static function textAsDocs($text, $spaces_number = 1) {
    // Removes spaces from the beginning and from the end.
    $text = trim($text);
    // Removes the ":" and line-endings from the beginning and from the end.
    $text = preg_replace("/^(:\r*\n+)|(\r*\n+)$/", '', $text);
    // Again, removes spaces from the beginning and from the end.
    $text = trim($text);
    // Replaces inner line-endings with spaces.
    $text = preg_replace("/\r*\n+/", ' ', $text);
    $text_lines = self::textToLines($text, 80 - ($spaces_number + 1));
    $text = '';
    // Format lines.
    foreach ($text_lines as $line) {
      $text .= "\r\n *" . str_repeat(' ', $spaces_number) . $line;
    }
    // Adds a break-character if no one is present.
    $pos = drupal_strlen($text) - 1;
    if (!isset(self::$breakChars[$text[$pos]])) {
      $text .= '.';
    }
    return $text;
  }

  /**
   * Split a text in lines.
   *
   * @param string $text
   *   text to be splitted.
   * @param int $max_chars_per_line
   *   max number of chars per line.
   *
   * @return array
   *   text lines.
   */
  public static function textToLines($text, $max_chars_per_line = 80) {
    // Removes spaces from the beginning and from the end of the source text.
    $text = trim($text);
    // Split the text by spaces (separated words).
    $words_arr = preg_split("/\s+/", $text);
    // Store the text splitted by lines.
    $lines_arr = array();
    // The row number being processed.
    $row_number = 0;
    // Store the number of words in the text minus 1.
    $words_num = count($words_arr) - 1;
    for ($idx = 0; $idx < $words_num; ++$idx) {
      @$lines_arr[$row_number] .= $words_arr[$idx];
      // Sum the number of chars in the current line plus the space char, plus
      // the chars in the next word.
      $future_line_lenght = drupal_strlen($lines_arr[$row_number]) + 1 + drupal_strlen($words_arr[$idx + 1]);
      // Append a space (words separator) to the current line if the next word
      // fits in the current line; start a new line otherwise.
      if ($future_line_lenght <= $max_chars_per_line) {
        $lines_arr[$row_number] .= ' ';
      }
      else {
        ++$row_number;
      }
    }
    @$lines_arr[$row_number] .= $words_arr[$words_num];
    return $lines_arr;
  }

  /**
   * Generate a trimmed, formatted version of a text.
   *
   * @param string $text
   *   the text for which the summary will be obtained.
   * @param int $max_chars
   *   the maximun character length of the summary.
   * @param int $tolerance
   *   the maximun number of characters allowed to be stripped from the end of
   *   the text.
   *
   * @return string
   *   the generated summary.
   */
  public static function textSummary($text, $max_chars, $tolerance = 100) {

    // Strip whitespaces from the beginning and end of the string.
    $text = trim($text);

    // If we have a short body, the entire body is the summary.
    if (drupal_strlen($text) <= $max_chars) {
      return $text;
    }

    // The summary may not be longer than maximum length specified. Initial
    // slice.
    $summary = drupal_substr($text, 0, $max_chars);

    // Store the reverse of the summary. We use strpos on the reversed needle
    // and haystack for speed and convenience.
    $reversed = strrev($summary);

    // Look for each break point, starting at the end of the summary.
    foreach (self::$breakChars as $point => $add_ellipsis) {
      // Find the first occurrence (actually the last, since $text is reversed)
      // of the point.
      $rpos = strpos($reversed, $point);
      // Don't slice with length 0 or with the tolerance exceeded.
      if ($rpos > 0 && $rpos <= $tolerance) {
        $summary = drupal_substr($summary, 0, -$rpos);
        if ($add_ellipsis) {
          $summary .= '...';
        }
        return $summary;
      }
    }

    return $summary . '...';
  }

  /**
   * Comment a given code.
   *
   * @param string $code
   *   the code to be commented.
   *
   * @return string
   *   the commented code.
   */
  public static function commentCode($code) {
    $code = preg_replace("/^./m", "//", $code);
    return $code;
  }

}
