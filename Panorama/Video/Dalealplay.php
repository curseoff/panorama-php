<?php
/**
 *  Copyright (C) 2011 by OpenHost S.L.
 *
 *  Permission is hereby granted, free of charge, to any person obtaining a copy
 *  of this software and associated documentation files (the "Software"), to deal
 *  in the Software without restriction, including without limitation the rights
 *  to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *  copies of the Software, and to permit persons to whom the Software is
 *  furnished to do so, subject to the following conditions:
 *
 *  The above copyright notice and this permission notice shall be included in
 *  all copies or substantial portions of the Software.
 *
 *  THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *  IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *  FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *  AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *  LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *  OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *  THE SOFTWARE.
 **/
/**
 * Wrapper class for Dalealplay videos
 *
 * @author Fran Diéguez <fran@openhost.es>
 * @version \$Id\$
 * @copyright OpenHost S.L., Mér Xuñ 01 15:58:58 2011
 * @package Panorama\Video
 **/
namespace Panorama\Video;

class Dalealplay  {

    /*
     * __construct()
     * @param $url
     */
    public function __construct($url)
    {

        $this->url = $url;
        $this->getVideoID();

    }

    /*
     * Fetchs the contents of the DaleAlPlay video page
     *
     */
    public function getPage()
    {
        if (!isset($this->page)) {
            $this->page = file_get_contents($this->url);
        }
        return $this->page;
    }

    /*
     * Sets the page contents, useful for using mocking objects
     *
     * @param $arg
     */
    public function setPage($page = '')
    {
        if (!empty($page)
            && !isset($this->page))
        {
            $this->page = $page;
        }
        return $this->page;
    }

    /*
     * Returns the title for this Dalealplay video
     *
     */
    public function getTitle()
    {
        if (!isset($this->title)) {
            preg_match('@<title>(.*)</title>@', $this->getPage(), $matches);
            $title = preg_split('@ - www.dalealplay.com@', $matches[1]);
            $title = $title[0];
            $this->title = iconv('ISO-8859-1', 'UTF-8', (string) $title);
        }
        return $this->title;
    }

    /*
     * Returns the thumbnail for this Dalealplay video
     *
     */
    public function getThumbnail()
    {
        if (!isset($this->thumbnail)) {
            $videoId = $this->getVideoId();
            $this->thumbnail = "http://thumbs.dalealplay.com/img/dap/{$videoId}/thumb";
        }
        return $this->thumbnail;
    }

    /*
     * Returns the duration in secs for this Dalealplay video
     *
     */
    public function getDuration()
    {
        return null;
    }

    /*
     * Returns the embed url for this Dalealplay video
     *
     */
    public function getEmbedUrl()
    {
        //@page.search("//link[@rel='video_src']").first.attributes["href"].sub("autoStart=true", "autoStart=false")
        if (!isset($this->embedUrl)) {
            preg_match('@rel="video_src"\shref="(.*)"@', $this->getPage(), $matches);
            $title = preg_replace('@autoStart=true@', 'autoStart=false', $matches[1]);
            $this->embedUrl = (string) $title;
        }
        return $this->embedUrl;
    }

    /*
     * Returns the HTML object to embed for this Dalealplay video
     *
     */
    public function getEmbedHTML($options = array())
    {
        $defaultOptions = array(
              'width' => 560,
              'height' => 349
              );

        $options = array_merge($defaultOptions, $options);
        unset($options['width']);
        unset($options['height']);

        // convert options into
        $htmlOptions = "";
        if (count($options) > 0) {
            foreach ($options as $key => $value ) {
                $htmlOptions .= "&" . $key . "=" . $value;
            }
        }

        return "<object type='application/x-shockwave-flash'
                        width='{$defaultOptions['width']}' height='{$defaultOptions['height']}'
                        data='{$this->getEmbedUrl()}'>
                    <param name='quality' value='best' />
                    <param name='allowfullscreen' value='true' />
                    <param name='scale' value='showAll' />
                    <param name='movie' value='{$this->getEmbedUrl()}' />
                </object>";

    }

    /*
     * Returns the FLV url for this Dalealplay video
     *
     */
    public function getFLV()
    {
        //"http://videos.dalealplay.com/contenidos3/#{CGI::parse(URI::parse(embed_url).query)['file']}"
        if (!isset($this->FLV)) {
            $this->FLV = '';
        }
        return $this->FLV;
    }

    /*
     * Returns the Download url for this Dalealplay video
     *
     */
    public function getDownloadUrl()
    {
        return null;
    }

    /*
     * Returns the name of the Video service
     *
     */
    public function getService()
    {
        return "Dalealplay";
    }

    /*
     * Calculates the Video ID from an Dalealplay URL
     *
     * @param $url
     */
    public function getVideoId()
    {

        if (!isset($this->videoId)) {
            $path = parse_url($this->url, PHP_URL_QUERY);
            preg_match("@con=(\w*)@", $path, $matches);
            $this->videoId = $matches[1];
        }
        return $this->videoId;

    }
}