<?php

/**
 * @file
 * Hooks provided by the PDF version module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Provide some information on the needs of the PDF library.
 *
 * @return
 *   Associative array with the following data:
 *   - name: name of the PDF library.
 *   - min_version: minimum version of the PDF library supported by the
 *     module.
 *   - url: URL where the PDF library can be downloaded from.
 *   - expand_css: boolean flag indicating whether to expand the CSS files
 *     in the HTML passed to the PDF library, or to leave it as a list of
 *     include directives.
 *   - public_dirs: directories to which the tool requires write-access,
 *     with configurable locations.
 *   - tool_dirs: directories to which the tool requires write-access, but
 *     can't be configured, and are relative to the tool's root path.
 *
 * @ingroup print_hooks
 */
function hook_pdf_tool_info() {
  return array(
    'name' => 'foopdf',
    'min_version' => '1.0',
    'url' => 'http://www.pdf.tool/download',
    'expand_css' => FALSE,
    'public_dirs' => array(
      'fonts',
      'cache',
      'tmp',
    ),
    'tool_dirs' => array(
      'xyz',
    ),
  );
}

/**
 * Find out the version of the PDF library
 *
 * @param string $pdf_tool
 *   Filename of the tool to be analysed.
 *
 * @return string
 *   version number of the library
 */
function hook_pdf_tool_version() {
  require_once(DRUPAL_ROOT . '/' . $pdf_tool);

  return '1.0';
}

/**
 * Generate a PDF version of the provided HTML.
 *
 * @param string $html
 *   HTML content of the PDF
 * @param array $meta
 *   Meta information to be used in the PDF
 *   - url: original URL
 *   - name: author's name
 *   - title: Page title
 *   - node: node object
 * @param string $filename
 *   (optional) Filename of the generated PDF
 *
 * @return
 *   generated PDF page, or NULL in case of error
 *
 * @see print_pdf_controller_html()
 * @ingroup print_hooks
 */
function hook_print_pdf_generate($html, $meta, $filename = NULL) {
  $pdf = new PDF();
  $pdf->writeHTML($html);
  if ($filename) {
    $pdf->Output($filename);
    return TRUE;
  }
  else {
    return $pdf->Output();
  }
}

/**
 * Alters the list of available PDF libraries.
 *
 * During the configuration of the PDF library to be used, the module needs
 * to discover and display the available libraries. This function should use
 * the internal _print_scan_libs() function which will scan both the module
 * and the libraries directory in search of the unique file pattern that can
 * be used to identify the library location.
 *
 * @param array $pdf_tools
 *   An associative array using as key the format 'module|path', and as value
 *   a string describing the discovered library, where:
 *   - module: the machine name of the module that handles this library.
 *   - path: the path where the library is installed, relative to DRUPAL_ROOT.
 *     If the recommended path is used, it begins with sites/all/libraries.
 *   As a recommendation, the value should contain in parantheses the path
 *   where the library was found, to allow the user to distinguish between
 *   multiple install paths of the same library version.
 *
 * @ingroup print_hooks
 */
function hook_print_pdf_available_libs_alter(&$pdf_tools) {
  module_load_include('inc', 'print', 'includes/print');
  $tools = _print_scan_libs('foo', '!^foo.php$!');

  foreach ($tools as $tool) {
    $pdf_tools['print_pdf_foo|' . $tool] = 'foo (' . dirname($tool) . ')';
  }
}

/**
 * Alters the PDF filename.
 *
 * Changes the value of the PDF filename variable, just before it is used to
 * create the file. When altering the variable, do not suffix it with the
 * '.pdf' extension, as the module will do that automatically.
 *
 * @param string $pdf_filename
 *   current value of the pdf_filename variable, after processing tokens and
 *   any transliteration steps.
 * @param string $path
 *   original alias/system path of the page being converted to PDF.
 *
 * @ingroup print_hooks
 */
function hook_print_pdf_filename_alter(&$pdf_filename, &$path) {
  $pdf_filename = 'foo';
}

/**
 * @} End of "addtogroup hooks".
 */
