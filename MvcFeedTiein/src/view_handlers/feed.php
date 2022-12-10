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
 * @package MvcFeedTiein
 */

/**
 * The view handler that uses XML feeds to render result objects.
 *
 * @package MvcFeedTiein
 * @version //autogentag//
 * @mainclass
 */
class ezcMvcFeedViewHandler implements ezcMvcViewHandler
{
    /**
     * Contains the zone name.
     *
     * @var string
     */
    protected $zoneName;

    /**
     * Contains the decorator.
     *
     * @var ezcMvcFeedDecorator
     */
    protected $decorator;

    /**
     * Contains the result after process() has been called.
     *
     * @var mixed
     */
    protected $result;

    /**
     * Contains the variables that will be available in the template.
     *
     * @var array(mixed)
     */
    protected $variables = array();

    /**
     * Contains the embedded feed object.
     *
     * @var ezcFeed
     */
    public $feed;

    /**
     * Contains the embedded feed type.
     *
     * @var string
     */
    protected $feedType;

    /**
     * Creates a new view handler, where $zoneName is the name of the block and
     * $decorator is the object implementing decorating methods for the feed
     * and feed items.
     *
     * @param string $zoneName
     * @param ezcMvcFeedDecorator $decorator
     */
    public function __construct( $zoneName, $decorator = null, $feedType = 'rss2' )
    {
        $this->zoneName = $zoneName;
        $this->decorator = $decorator;
        $this->feed = new ezcFeed;
        $this->feedType = $feedType;
    }

    /**
     * Adds a variable to the template, which can then be used for rendering
     * the view.
     *
     * @param string $name
     * @param mixed $value
     */
    public function send( $name, $value )
    {
        $this->variables[$name] = $value;
    }

    /**
     * Processes the template with the variables added by the send() method.
     * The result of this action should be retrievable through the getResult() method.
     *
     * @param mixed $last
     */
    public function process( $last )
    {
        $feed = new ezcFeed;
        $this->decorator->decorateFeed( $feed );

        foreach ( $this->variables[$this->decorator->getItemVariable()] as $itemData )
        {
            $item = $feed->add( 'item' );
            $this->decorator->decorateFeedItem( $item, $itemData );
        }
        $this->result = $feed->generate( $this->feedType );
    }

    /**
     * Returns the value of the property $name.
     *
     * @throws ezcBasePropertyNotFoundException if the property does not exist.
     * @param string $name
     * @return mixed
     * @ignore
     */
    public function __get( $name )
    {
        return $this->variables[$name];
    }

    /**
     * Returns true if the property $name is set, otherwise false.
     *
     * @param string $name
     * @return bool
     * @ignore
     */
    public function __isset( $name )
    {
        return array_key_exists( $name, $this->variables );
    }

    /**
     * Returns the name of the template, as set in the constructor.
     *
     * @return string
     */
    public function getName()
    {
        return $this->zoneName;
    }

    /**
     * Returns the result of the process() method.
     *
     * @return mixed
     */
    public function getResult()
    {
        return $this->result;
    }
}
?>
