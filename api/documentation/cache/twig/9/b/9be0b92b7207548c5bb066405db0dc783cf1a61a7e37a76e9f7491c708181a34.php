<?php

/* partials/base.html.twig */
class __TwigTemplate_9be0b92b7207548c5bb066405db0dc783cf1a61a7e37a76e9f7491c708181a34 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'stylesheets' => array($this, 'block_stylesheets'),
            'javascripts' => array($this, 'block_javascripts'),
            'sidebar' => array($this, 'block_sidebar'),
            'body' => array($this, 'block_body'),
            'content' => array($this, 'block_content'),
            'footer' => array($this, 'block_footer'),
            'navigation' => array($this, 'block_navigation'),
            'analytics' => array($this, 'block_analytics'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        $context["theme_config"] = $this->getAttribute($this->getAttribute((isset($context["config"]) ? $context["config"] : null), "themes", array()), $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["config"]) ? $context["config"] : null), "system", array()), "pages", array()), "theme", array()));
        // line 2
        echo "<!DOCTYPE html>
<html lang=\"en\">
<head>
    ";
        // line 5
        $this->displayBlock('head', $context, $blocks);
        // line 52
        echo "</head>
<body class=\"searchbox-hidden ";
        // line 53
        echo $this->getAttribute($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "header", array()), "body_classes", array());
        echo "\" data-url=\"";
        echo $this->getAttribute((isset($context["page"]) ? $context["page"] : null), "route", array());
        echo "\">
    <div class=\"main-wrapper\">
        ";
        // line 55
        $this->displayBlock('sidebar', $context, $blocks);
        // line 70
        echo "
        ";
        // line 71
        $this->displayBlock('body', $context, $blocks);
        // line 98
        echo "        ";
        $this->displayBlock('analytics', $context, $blocks);
        // line 99
        echo "    </div>
 </body>
</html>
";
    }

    // line 5
    public function block_head($context, array $blocks = array())
    {
        // line 6
        echo "    <meta charset=\"utf-8\" />
    <title>";
        // line 7
        if ($this->getAttribute((isset($context["header"]) ? $context["header"] : null), "title", array())) {
            echo $this->getAttribute((isset($context["header"]) ? $context["header"] : null), "title", array());
            echo " | ";
        }
        echo $this->getAttribute((isset($context["site"]) ? $context["site"] : null), "title", array());
        echo "</title>
    ";
        // line 8
        $this->loadTemplate("partials/metadata.html.twig", "partials/base.html.twig", 8)->display($context);
        // line 9
        echo "    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no\" />
    <link rel=\"alternate\" type=\"application/atom+xml\" href=\"";
        // line 10
        echo (isset($context["base_url_absolute"]) ? $context["base_url_absolute"] : null);
        echo "/feed:atom\" title=\"Atom Feed\" />
    <link rel=\"alternate\" type=\"application/rss+xml\" href=\"";
        // line 11
        echo (isset($context["base_url_absolute"]) ? $context["base_url_absolute"] : null);
        echo "/feed:rss\" title=\"RSS Feed\" />
    <link rel=\"icon\" type=\"image/ico\" href=\"";
        // line 12
        echo $this->env->getExtension('GravTwigExtension')->urlFunc("theme://images/favicon.ico");
        echo "\">

    ";
        // line 14
        $this->displayBlock('stylesheets', $context, $blocks);
        // line 29
        echo "
    ";
        // line 30
        $this->displayBlock('javascripts', $context, $blocks);
        // line 50
        echo "
    ";
    }

    // line 14
    public function block_stylesheets($context, array $blocks = array())
    {
        // line 15
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addCss", array(0 => "theme://css-compiled/nucleus.css", 1 => 102), "method");
        // line 16
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addCss", array(0 => "theme://css-compiled/theme.css", 1 => 101), "method");
        // line 17
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addCss", array(0 => "theme://css/custom.css", 1 => 100), "method");
        // line 18
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addCss", array(0 => "theme://css/font-awesome.min.css", 1 => 100), "method");
        // line 19
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addCss", array(0 => "theme://css/featherlight.min.css"), "method");
        // line 20
        echo "
        ";
        // line 21
        if (((($this->getAttribute((isset($context["browser"]) ? $context["browser"] : null), "getBrowser", array()) == "msie") && ($this->getAttribute((isset($context["browser"]) ? $context["browser"] : null), "getVersion", array()) >= 8)) && ($this->getAttribute((isset($context["browser"]) ? $context["browser"] : null), "getVersion", array()) <= 9))) {
            // line 22
            echo "            ";
            $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addCss", array(0 => "theme://css/nucleus-ie9.css"), "method");
            // line 23
            echo "            ";
            $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addCss", array(0 => "theme://css/pure-0.5.0/grids-min.css"), "method");
            // line 24
            echo "        ";
        }
        // line 25
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addCss", array(0 => "theme://css/swagger/screen.css"), "method");
        // line 26
        echo "
        ";
        // line 27
        echo $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "css", array(), "method");
        echo "
    ";
    }

    // line 30
    public function block_javascripts($context, array $blocks = array())
    {
        // line 31
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "jquery", 1 => 101), "method");
        // line 32
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/modernizr.custom.71422.js", 1 => 100), "method");
        // line 33
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/featherlight.min.js"), "method");
        // line 34
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/learn.js"), "method");
        // line 35
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/jquery.slideto.min.js"), "method");
        // line 36
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/jquery.wiggle.min.js"), "method");
        // line 37
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/jquery.ba-bbq.min.js"), "method");
        // line 38
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/handlebars-2.0.0.js"), "method");
        // line 39
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/underscore-min.js"), "method");
        // line 40
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/backbone-min.js"), "method");
        // line 41
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/swagger-ui.js"), "method");
        // line 42
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/highlight.7.3.pack.js"), "method");
        // line 43
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/marked.js"), "method");
        // line 44
        echo "        ";
        $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/swagger/swagger-oauth.js"), "method");
        // line 45
        echo "        ";
        if (((($this->getAttribute((isset($context["browser"]) ? $context["browser"] : null), "getBrowser", array()) == "msie") && ($this->getAttribute((isset($context["browser"]) ? $context["browser"] : null), "getVersion", array()) >= 8)) && ($this->getAttribute((isset($context["browser"]) ? $context["browser"] : null), "getVersion", array()) <= 9))) {
            // line 46
            echo "            ";
            $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "addJs", array(0 => "theme://js/html5shiv-printshiv.min.js"), "method");
            // line 47
            echo "        ";
        }
        // line 48
        echo "        ";
        echo $this->getAttribute((isset($context["assets"]) ? $context["assets"] : null), "js", array(), "method");
        echo "
    ";
    }

    // line 55
    public function block_sidebar($context, array $blocks = array())
    {
        // line 56
        echo "        <nav id=\"sidebar\">
            <div id=\"header\">
                <a id=\"logo\" href=\"";
        // line 58
        echo (($this->getAttribute((isset($context["theme_config"]) ? $context["theme_config"] : null), "home_url", array())) ? ($this->getAttribute((isset($context["theme_config"]) ? $context["theme_config"] : null), "home_url", array())) : ((isset($context["base_url_absolute"]) ? $context["base_url_absolute"] : null)));
        echo "\" style=\"font-size:50px; color:white;\">Flipit<!--img src=\"";
        echo $this->env->getExtension('GravTwigExtension')->urlFunc("theme://images/site_logo.png");
        echo "\" --></a>
                <div class=\"searchbox\">
                    <label for=\"search-by\"><i class=\"fa fa-search\"></i></label>
                    <input id=\"search-by\" type=\"text\" placeholder=\"Search Documentation\" data-search-input=\"";
        // line 61
        echo (isset($context["base_url_relative"]) ? $context["base_url_relative"] : null);
        echo "/search.json/query\" />
                    <span data-search-clear><i class=\"fa fa-close\"></i></span>
                </div>
            </div>
            <div class=\"padding\">
            ";
        // line 66
        $this->loadTemplate("partials/sidebar.html.twig", "partials/base.html.twig", 66)->display($context);
        // line 67
        echo "            </div>
        </nav>
        ";
    }

    // line 71
    public function block_body($context, array $blocks = array())
    {
        // line 72
        echo "        <section id=\"body\">
            <div id=\"overlay\"></div>

            <div class=\"padding wrapper\">
                <a href=\"#\" id=\"sidebar-toggle\" data-sidebar-toggle><i class=\"fa fa-2x fa-bars\"></i></a>

                ";
        // line 78
        if ((($this->getAttribute($this->getAttribute((isset($context["theme_config"]) ? $context["theme_config"] : null), "github", array()), "position", array()) == "top") || $this->getAttribute($this->getAttribute($this->getAttribute((isset($context["config"]) ? $context["config"] : null), "plugins", array()), "breadcrumbs", array()), "enabled", array()))) {
            // line 79
            echo "                <div id=\"top-bar\">
                    ";
            // line 80
            if ($this->getAttribute($this->getAttribute($this->getAttribute((isset($context["config"]) ? $context["config"] : null), "plugins", array()), "breadcrumbs", array()), "enabled", array())) {
                // line 81
                echo "                    ";
                $this->loadTemplate("partials/breadcrumbs.html.twig", "partials/base.html.twig", 81)->display($context);
                // line 82
                echo "                    ";
            }
            // line 83
            echo "                </div>
                ";
        }
        // line 85
        echo "
                ";
        // line 86
        $this->displayBlock('content', $context, $blocks);
        // line 87
        echo "
                ";
        // line 88
        $this->displayBlock('footer', $context, $blocks);
        // line 93
        echo "
            </div>
            ";
        // line 95
        $this->displayBlock('navigation', $context, $blocks);
        // line 96
        echo "        </section>
        ";
    }

    // line 86
    public function block_content($context, array $blocks = array())
    {
    }

    // line 88
    public function block_footer($context, array $blocks = array())
    {
        // line 89
        echo "                    ";
        if (($this->getAttribute($this->getAttribute((isset($context["theme_config"]) ? $context["theme_config"] : null), "github", array()), "position", array()) == "bottom")) {
            // line 90
            echo "                    ";
            $this->loadTemplate("partials/github_note.html.twig", "partials/base.html.twig", 90)->display($context);
            // line 91
            echo "                    ";
        }
        // line 92
        echo "                ";
    }

    // line 95
    public function block_navigation($context, array $blocks = array())
    {
    }

    // line 98
    public function block_analytics($context, array $blocks = array())
    {
        $this->loadTemplate("partials/analytics.html.twig", "partials/base.html.twig", 98)->display($context);
    }

    public function getTemplateName()
    {
        return "partials/base.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  319 => 98,  314 => 95,  310 => 92,  307 => 91,  304 => 90,  301 => 89,  298 => 88,  293 => 86,  288 => 96,  286 => 95,  282 => 93,  280 => 88,  277 => 87,  275 => 86,  272 => 85,  268 => 83,  265 => 82,  262 => 81,  260 => 80,  257 => 79,  255 => 78,  247 => 72,  244 => 71,  238 => 67,  236 => 66,  228 => 61,  220 => 58,  216 => 56,  213 => 55,  206 => 48,  203 => 47,  200 => 46,  197 => 45,  194 => 44,  191 => 43,  188 => 42,  185 => 41,  182 => 40,  179 => 39,  176 => 38,  173 => 37,  170 => 36,  167 => 35,  164 => 34,  161 => 33,  158 => 32,  155 => 31,  152 => 30,  146 => 27,  143 => 26,  140 => 25,  137 => 24,  134 => 23,  131 => 22,  129 => 21,  126 => 20,  123 => 19,  120 => 18,  117 => 17,  114 => 16,  111 => 15,  108 => 14,  103 => 50,  101 => 30,  98 => 29,  96 => 14,  91 => 12,  87 => 11,  83 => 10,  80 => 9,  78 => 8,  70 => 7,  67 => 6,  64 => 5,  57 => 99,  54 => 98,  52 => 71,  49 => 70,  47 => 55,  40 => 53,  37 => 52,  35 => 5,  30 => 2,  28 => 1,);
    }
}
