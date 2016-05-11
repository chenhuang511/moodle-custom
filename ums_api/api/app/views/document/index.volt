<section>
    <div class="container">
        <ul class="docs-nav">
            <li><strong>Getting Started</strong></li>
            <li><a href="#welcome" class="cc-active">Welcome</a></li>
            <li><a href="#benefits" class="cc-active">Benefits</a></li>
            <li><a href="#features" class="cc-active">Features</a></li>
            <li><a href="#license" class="cc-active">License</a></li>
            <li class="separator"></li>
            <li><strong>Customization</strong></li>
            <li><a href="#view_type" class="cc-active">View Type</a></li>
            <li><a href="#animation_style" class="cc-active">Animation Styles</a></li>
            <li><a href="#bars_text" class="cc-active">Bars Text</a></li>
            <li><a href="#vote_counter" class="cc-active">Vote Counter</a></li>
            <li><a href="#rating_icons" class="cc-active">Rating Icons</a></li>
            <li><a href="#rating_titles" class="cc-active">Rating Titles</a></li>
            <li><a href="#bar_colors" class="cc-active">Bar Colors</a></li>
        </ul>
        <div class="docs-content">
            <h2> Getting Started</h2>
            <h3 id="welcome"> Welcome</h3>
            <p>Tài liệu hướng dẫn kết nối hệ thống UMS (User Management System)</p>
            <h3 id="benefits"> Benefits</h3>
            <ul>
                <li>Use Opineo to conduct pre-release surveys before launching new products or services</li>
                <li>Analyze new trends in the industry</li>
                <li>Run crowd-sourced campaigns with Opineo to mature new ideas, website designs or
                    logos
                </li>
                <li>Conduct popularity surveys with free Opineo widget</li>
                <li>Create and host polling surveys with Opineo</li>
            </ul>
            <h3 id="features"> Features</h3>
            <ul>
                <li>Facility to customize to match your website theme</li>
                <li>Detailed and Compact view options</li>
                <li>Comprehensive options to customize animation, colors, orientation and style</li>
                <li>All the power and flexibility of jQuery</li>
                <li>Easy install; 100% integration</li>
                <li>Facility to customize rating icons</li>
            </ul>
            <h3 id="license"> License</h3>
            <p> This Feedback Collection and Polling widget is free for personal and commercial
                projects as long as you are providing a link back to this page. If you don’t want
                to provide a link back, simply contribute to the development and improvement of
                this tool. To contribute to this and many other interesting projects, go to Support
                Efforts and become a patron.</p>
            <hr>
            <h2> Customizing Opineo</h2>
            <p> Before you make any cusomization or even start using Opineo create a 'div' element
                and assign it some id. Now add references to necessary Javascrip files e.g.</p>
            <ul>
                <li>A reference to latest jQuery library</li>
                <li>A reference to Opineo script file sudo nano opineo.js</li>
            </ul>
            <p> The following customization options are available in Opineo:</p>
            <h3 id="view_type"> View Type</h3>
            <ul>
                <li>Detailed View</li>
            </ul>

      <pre class="prettyprint">&lt;script&gt;
      	$(document).ready(function (){
        $('#DefaultOptions').opineo('results.php', {curvalue:3,
            view: 'detailed',
            animation_speed: 'super',
            show_total_votes_counter: false,
            show_overall_rating: true});
         })
&lt;/script&gt;</pre>
            <ul>
                <li>Compact View</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
	$(document).ready(function (){
	$('#CompactView').opineo('results.php',
    	    {curvalue:3, view: 'compact',
    	    animation_speed: 'super'});
	})
&lt;/script&gt;</pre>
            <ul>
                <li>Mini View</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#MiniView').opineo('results.php', {curvalue:0, view: 'mini', callback: myCallback});
    })
&lt;/script&gt;</pre>
            <h3 id="animation_style"> Animation Style</h3>
            <ul>
                <li>Slow</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', animation_speed:'slow'});
    })
&lt;/script&gt; </pre>
            <ul>
                <li>Medium</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', animation_speed:'mild'});
    })
&lt;/script&gt; </pre>
            <ul>
                <li>Fast</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', animation_speed:'fast'});
    })
 &lt;/script&gt;</pre>
            <ul>
                <li>Super Fast</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', animation_speed:'super'});
    })
&lt;/script&gt;</pre>
            <ul>
                <li>Ultra Fast</li>
            </ul>
      <pre class="prettyprint"> &lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', animation_speed:'ultra fast'});
    })
&lt;/script&gt; </pre>
            <h3 id="bars_text"> Bars Text</h3>
            <ul>
                <li>Show</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', votes_label:true});
    })
&lt;/script&gt;</pre>
            <ul>
                <li>Hide</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', votes_label:false});
    })
 &lt;/script&gt;</pre>
            <h3 id="vote_counter"> Vote Counter</h3>
            <ul>
                <li>Show</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', show_votes_counter:true});
    })
&lt;/script&gt; </pre>
            <ul>
                <li>Hide</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', votes_label:false});
    })
&lt;/script&gt;</pre>
            <h3 id="rating_icons"> Rating Icons</h3>
            <ul>
                <li>Colored</li>
                <li>Grey</li>
            </ul>
            <h3 id="rating_titles"> Rating Titles</h3>
            <ul>
                <li>Text For First Star</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', star_1_text:'Hate It'});
    })
&lt;/script&gt;</pre>
            <ul>
                <li>Text For Second Star</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', star_2_text:'Don't Like It'});
    })
&lt;/script&gt; </pre>
            <ul>
                <li>Text For Third Star</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', star_1_text:'Its OK'});
    })
&lt;/script&gt;</pre>
            <ul>
                <li>Text For Fourth Star</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', star_1_text:'Like It'});
    })
&lt;/script&gt;</pre>
            <ul>
                <li>Text For Fifth Star</li>
            </ul>
      <pre class="prettyprint">&lt;script&gt;
    $(document).ready(function (){
    $('#opineo').opineo('results.php', {curvalue:0, view: 'mini', star_1_text:'Love It'});
    })
&lt;/script&gt;</pre>
            <h3 id="bar_colors"> Bar Colors</h3>
            <ul>
                <li>Colors of Red Bar</li>
                <li>Colors of Yellow Bar</li>
                <li>Colors of Green Bar</li>
            </ul>
        </div>
    </div>
</section>