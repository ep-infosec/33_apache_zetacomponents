<?php
/**
 *
 * Licensed to the Apache Software Foundation (ASF) under one
 * or more contributor license agreements.  See the NOTICE file
 * distributed with this work for additional information
 * regarding copyright ownership.  The ASF licenses this file
 * to you under the Apache License, Version 2.0 (the
 * "License"); you may not use this file except in compliance
 * with the License.  You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing,
 * software distributed under the License is distributed on an
 * "AS IS" BASIS, WITHOUT WARRANTIES OR CONDITIONS OF ANY
 * KIND, either express or implied.  See the License for the
 * specific language governing permissions and limitations
 * under the License.
 *
 * @license http://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
 * @version //autogentag//
 * @filesource
 * @package Translation
 */

/**
 * ezcTranslationManager handles a specific translation file and provides
 * functionality to apply filters and retrieve contexts and translations.
 *
 * The following example shows typical usage:
 * <code>
 * <?php
 * $a = new ezcTranslationTsBackend( 'tests/translations' );
 * $a->setOptions( array ( 'format' => '[LOCALE].xml' ) );
 *
 * $b = new ezcTranslationManager( $a );
 * $b->addFilter( ezcTranslationComplementEmptyFilter::getInstance() );
 * $b->addFilter( ezcTranslationBorkFilter::getInstance() );
 *
 * // Asks the backend for data, runs the attached filter, and creates a
 * // Translation object
 * $tln1 = $b->getContext( 'nl_NL', 'design/admin/content/browse_copy_node' );
 *
 * // Returns the localized string belonging to key "key", the filter has already
 * // been applied to it. Possible parameters can be passed as associative array
 * // as optional second parameter.
 * $string = $tln1->getTranslation( 'Choose location for copy of <%object_name>', array( 'object_name' => 'Foo' ) );
 * ?>
 * </code>
 *
 * @package Translation
 * @version //autogentag//
 * @mainclass
 */
class ezcTranslationManager
{
    /**
     * An array containing the filters that should be applies
     * whenever a translation context is requested.
     *
     * @var array(string)
     */
    private $filters;

    /**
     * The backend in use which is responsible for reading in
     * the context and returning it to the manager.
     *
     * @var ezcTranslationBackendInterface
     */
    private $backend;

    /**
     * Context cache.
     *
     * Two dimension array with the locale and the contextname as indexes.
     *
     * @var array(string=>array(string=>ezcTranslation))
     */
    private $contextCache;

    /**
     * Constructs an ezcTranslationManager object
     *
     * This constructor constructs a new ezcTranslationManager object. The only
     * parameter is a class that implements the ezcTranslationBackendInterface.
     *
     * @param ezcTranslationBackend $backend An instance of a translation
     *                                       backend.
     */
    function __construct( ezcTranslationBackend $backend )
    {
        $this->backend = $backend;
        $this->filters = array();
    }

    /**
     * Adds a filter to the filter list.
     *
     * This methods adds the passed filter object to the list of $filters that
     * will be applied on every context before being returned by getContext().
     *
     * @param ezcTranslationFilter $filter
     */
    public function addFilter( ezcTranslationFilter $filter )
    {
        $this->filters[] = $filter;
    }

    /**
     * Returns the translation for the context $context with the locale $locale.
     *
     * Null is returned if no such context exists.
     *
     * @param string $locale
     * @param string $context
     * @return ezcTranslation
     */
    private function getFromCache( $locale, $context )
    {
        if ( isset( $this->contextCache[$locale][$context] ) )
        {
            return $this->contextCache[$locale][$context];
        }
        return null;
    }

    /**
     * Stores $contextData with the name $contextName and locale $locale
     * to the context cache
     *
     * @param string $locale
     * @param string $contextName
     * @param ezcTranslation $contextData
     * @return void
     */
    private function putToCache( $locale, $contextName, ezcTranslation $contextData )
    {
        $this->contextCache[$locale][$contextName] = $contextData;
    }

    /**
     * Returns the translations for the $context context and the locale $locale.
     *
     * This methods reads the context data from the backend and applies the
     * filters before returning the translation map as part of the
     * ezcTranslation object that it returns.
     *
     * @param string $locale
     * @param string $context
     * @return ezcTranslation
     */
    public function getContext( $locale, $context )
    {
        // Try to find the context in the cache
        if ( ( $translationContext = $this->getFromCache( $locale, $context) ) !== null )
        {
            return $translationContext;
        }
        // Retrieve the context through the backend and apply filters
        $translationContext = $this->backend->getContext( $locale, $context );
        foreach ( $this->filters as $filter )
        {
            $filter->runFilter( $translationContext );
        }
        // Create the object, put it in the cache and return it
        $returnContext = new ezcTranslation( $translationContext );
        $this->putToCache( $locale, $context, $returnContext );
        return $returnContext;
    }
}

?>
