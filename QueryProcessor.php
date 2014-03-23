<?php
/**
 * This class has methods to manipulate files and directories.
 * Another functionality is to extract words from files, count words
 * occurrence and calculate distance between them. 
 * 
 * The query functionality uses a inverted index to store words
 * and make queries. See more about inverted index at http://en.wikipedia.org/wiki/Inverted_index
 *
 * @author Daniel Thales Naves <daniel.naves@outlook.com>
 */
class QueryProcessor {

	/**
     * List all files inside directory and return them in array.
     *
	 * @param string Directory path
     * @return array returns the file list
     */
	public static function listDirectoryFiles($directoryPath) {
		if ($handle = opendir($directoryPath)) {
			$files = array();
			while (false !== ($file = readdir($handle))) {
				if ($file != '.' && $file != '..')
					$files[] = $file;
			}
			closedir($handle);
		}
		return $files;
	}

	/**
     * Count same words in a file list and returns an array by occurrence order.
     *
	 * @param array File list
	 * @param string File path
     * @return array returns the word list by occurrence order
     */
	public static function countSameWords($fileList, $filePath) {
		$content = '';
		$nContent = array();
		foreach ($fileList as $index => $fileName) {
			$content    = file_get_contents($filePath .''. $fileName);
			$content    = html_entity_decode($content);
			$content    = strtoupper($content);
			$content    = strip_tags($content);
			$content    = str_word_count($content, 1);
			$keysContent = $content;
			$content    = array_count_values($content);
			/*
				Inverted file structure
				[WORD] => array (
					[docID] => (pos1, pos2, pos3, ..., posN),
					[docID] => (pos1, pos2, pos3, ..., posN),
					[docID] => (pos1, pos2, pos3, ..., posN),
					[docID] => (pos1, pos2, pos3, ..., posN),
					[docID] => (pos1, pos2, pos3, ..., posN)
				)	
			*/
			foreach ($content as $k => $v) {
				if (array_key_exists($k, $nContent)) {
					//$nContent[WORD][ID_ARQUIVO] 
					$keys = array_keys($keysContent, $k);
					$nContent[$k][$index] = $keys; 
				} else {
					$nContent[$k] = array();
					$keys = array_keys($keysContent, $k);
					$nContent[$k][$index] = $keys; 
				}
			}
		}
		ksort($nContent);
		return $nContent;
	}

	/**
     * Remove the first array element and maintain associative index. Function array_kshift remove
     * associative index and sets a integer value.
     *
	 * @param array associative array
     * @return array returns an integer index array
     */
	public static function array_kshift(&$arr) {
		list($k) = array_keys($arr);
		$r  = array($k => $arr[$k]);
		unset($arr[$k]);
		return $r;
	}

	/**
     * Makes intersection using the words position.
     *
	 * @param array word with positions
	 * @param array word with positions
     * @return array returns intersection between two words
     */
	public static function positionalIntersection($p1, $p2) {
		//removes a document list that aren't in both words
		$keys = array_keys(array_intersect_key($p1, $p2));
		$result = array();
		foreach ($keys as $key) {
			foreach ($p1[$key] as $k => $v) {
				//verify if one word is after another
				if (in_array($v+1, $p2[$key])) {
					$result[$key][] = $v+1;
				}
			}
		}
		return $result;
	}

	/**
     * Receives a query and find statements at inverted index.
     *
	 * @param string words sequences separated by white spaces
	 * @param array inverted index with words
	 * @param string 'and' for conjunctive queries and 'or' for disjunctive queries
     * @return array returns a file list with a matched query
     */
	public static function processQuery($query, $invertedIndex, $type) {
		$queryArray = explode(' ', $query);
		$result = array();
		if (sizeof($queryArray) == 0) {
			return false;
		}
		else if (sizeof($queryArray) == 1) {
			if (array_key_exists($queryArray[0], $invertedIndex)) {
				return $invertedIndex[$queryArray[0]];
			} else {
				return false;
			}
		} else {
			$size = sizeof($queryArray);
			//verify if all terms are at inverted index
			foreach ($queryArray as $queryTerm) {
				if (!array_key_exists($queryTerm, $invertedIndex)) {
					return false;
				}
			}
			for ($i = 1; $i < $size; $i++) {
				if (empty($result)) {
					$result = $type == 'and' ? self::positionalIntersection($invertedIndex[$queryArray[0]], $invertedIndex[$queryArray[1]]) : uniao($invertedIndex[$queryArray[0]], $invertedIndex[$queryArray[1]]);
				} else {
					$result = $type == 'and' ? self::positionalIntersection($result, $invertedIndex[$queryArray[$i]]) : uniao($result, $invertedIndex[$queryArray[$i]]);
				}
			}
			return $result;
		}
	}
}
?>