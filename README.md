## Query Processor

A query processor class that extract words from hard disk files, creates a inverted index with words found and calculates their positions and count ocurrences.

I created an example using a set of HTML files with portuguese text, the example basically has a form for write words sequence. After search the file list with words or terms, you can see a file list matched and access them.

More about inverted index at: [Inverted Index](http://en.wikipedia.org/wiki/Inverted_index)

## Example

For running the example put 'example' directory at web server, open file file 'index.php' and alter folder and files paths at variables: $FOLDER_PATH and $INVERTED_INDEX_PATH.

![alt text](https://raw.githubusercontent.com/danieltnaves/QueryProcessor/master/screenshot.png "Example screenshot")
