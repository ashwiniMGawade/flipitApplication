<?php

/* partials/sidebar.html.twig */
class __TwigTemplate_8fea9822f78bfcac3fb5ed484139ca317afa762eda5ba65b749910721999c111 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 23
        echo "
";
        // line 36
        echo "
";
        // line 37
        if ($this->getAttribute((isset($context["theme_config"]) ? $context["theme_config"] : null), "top_level_version", array())) {
            // line 38
            echo "    ";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute((isset($context["pages"]) ? $context["pages"] : null), "children", array()));
            foreach ($context['_seq'] as $context["slug"] => $context["ver"]) {
                // line 39
                echo "        ";
                echo $this->getAttribute($this, "version", array(0 => $context["ver"]), "method");
                echo "
        <ul id=\"";
                // line 40
                echo $context["slug"];
                echo "\" class=\"topics\">
        ";
                // line 41
                echo $this->getAttribute($this, "loop", array(0 => $context["ver"], 1 => ""), "method");
                echo "
        </ul>
    ";
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['slug'], $context['ver'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } else {
            // line 45
            echo "    <ul class=\"topics\">
    ";
            // line 46
            echo $this->getAttribute($this, "loop", array(0 => (isset($context["pages"]) ? $context["pages"] : null), 1 => ""), "method");
            echo "
    </ul>
";
        }
        // line 49
        echo "

<hr />

<a class=\"padding\" href=\"#\" data-clear-history-toggle><i class=\"fa fa-fw fa-history\"></i> Clear History</a><br />

";
    }

    // line 1
    public function getloop($__page__ = null, $__parent_loop__ = null)
    {
        $context = $this->env->mergeGlobals(array(
            "page" => $__page__,
            "parent_loop" => $__parent_loop__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 2
            echo "    ";
            if ((twig_length_filter($this->env, (isset($context["parent_loop"]) ? $context["parent_loop"] : null)) > 0)) {
                // line 3
                echo "        ";
                $context["data_level"] = (isset($context["parent_loop"]) ? $context["parent_loop"] : null);
                // line 4
                echo "    ";
            } else {
                // line 5
                echo "        ";
                $context["data_level"] = 0;
                // line 6
                echo "    ";
            }
            // line 7
            echo "    ";
            $context['_parent'] = (array) $context;
            $context['_seq'] = twig_ensure_traversable($this->getAttribute($this->getAttribute((isset($context["page"]) ? $context["page"] : null), "children", array()), "visible", array()));
            $context['loop'] = array(
              'parent' => $context['_parent'],
              'index0' => 0,
              'index'  => 1,
              'first'  => true,
            );
            if (is_array($context['_seq']) || (is_object($context['_seq']) && $context['_seq'] instanceof Countable)) {
                $length = count($context['_seq']);
                $context['loop']['revindex0'] = $length - 1;
                $context['loop']['revindex'] = $length;
                $context['loop']['length'] = $length;
                $context['loop']['last'] = 1 === $length;
            }
            foreach ($context['_seq'] as $context["_key"] => $context["p"]) {
                // line 8
                echo "        ";
                $context["parent_page"] = (($this->getAttribute($context["p"], "activeChild", array())) ? (" parent") : (""));
                // line 9
                echo "        ";
                $context["current_page"] = (($this->getAttribute($context["p"], "active", array())) ? (" active") : (""));
                // line 10
                echo "        <li class=\"dd-item";
                echo (isset($context["parent_page"]) ? $context["parent_page"] : null);
                echo (isset($context["current_page"]) ? $context["current_page"] : null);
                echo "\" data-nav-id=\"";
                echo $this->getAttribute($context["p"], "route", array());
                echo "\">
            <a href=\"";
                // line 11
                echo $this->getAttribute($context["p"], "url", array());
                echo "\">
                <i class=\"fa fa-check read-icon\"></i>
                <span><b>";
                // line 13
                if (((isset($context["data_level"]) ? $context["data_level"] : null) == 0)) {
                    echo $this->getAttribute($context["loop"], "index", array());
                    echo ". ";
                }
                echo "</b>";
                echo $this->getAttribute($context["p"], "menu", array());
                echo "</span>
            </a>
            ";
                // line 15
                if (($this->getAttribute($this->getAttribute($context["p"], "children", array()), "count", array()) > 0)) {
                    // line 16
                    echo "            <ul>
                ";
                    // line 17
                    echo $this->getAttribute($this, "loop", array(0 => $context["p"], 1 => ((isset($context["parent_loop"]) ? $context["parent_loop"] : null) + $this->getAttribute($context["loop"], "index", array()))), "method");
                    echo "
            </ul>
            ";
                }
                // line 20
                echo "        </li>
    ";
                ++$context['loop']['index0'];
                ++$context['loop']['index'];
                $context['loop']['first'] = false;
                if (isset($context['loop']['length'])) {
                    --$context['loop']['revindex0'];
                    --$context['loop']['revindex'];
                    $context['loop']['last'] = 0 === $context['loop']['revindex0'];
                }
            }
            $_parent = $context['_parent'];
            unset($context['_seq'], $context['_iterated'], $context['_key'], $context['p'], $context['_parent'], $context['loop']);
            $context = array_intersect_key($context, $_parent) + $_parent;
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    // line 24
    public function getversion($__p__ = null)
    {
        $context = $this->env->mergeGlobals(array(
            "p" => $__p__,
        ));

        $blocks = array();

        ob_start();
        try {
            // line 25
            echo "    ";
            $context["parent_page"] = (($this->getAttribute((isset($context["p"]) ? $context["p"] : null), "activeChild", array())) ? (" parent") : (""));
            // line 26
            echo "    ";
            $context["current_page"] = (($this->getAttribute((isset($context["p"]) ? $context["p"] : null), "active", array())) ? (" active") : (""));
            // line 27
            echo "    <h5 class=\"";
            echo (isset($context["parent_page"]) ? $context["parent_page"] : null);
            echo (isset($context["current_page"]) ? $context["current_page"] : null);
            echo "\">
        ";
            // line 28
            if (($this->getAttribute((isset($context["p"]) ? $context["p"] : null), "activeChild", array()) || $this->getAttribute((isset($context["p"]) ? $context["p"] : null), "active", array()))) {
                // line 29
                echo "        <i class=\"fa fa-chevron-down fa-fw\"></i>
        ";
            } else {
                // line 31
                echo "        <i class=\"fa fa-plus fa-fw\"></i>
        ";
            }
            // line 33
            echo "        <a href=\"";
            echo $this->getAttribute((isset($context["p"]) ? $context["p"] : null), "url", array());
            echo "\">";
            echo $this->getAttribute((isset($context["p"]) ? $context["p"] : null), "menu", array());
            echo "</a>
    </h5>
";
        } catch (Exception $e) {
            ob_end_clean();

            throw $e;
        }

        return ('' === $tmp = ob_get_clean()) ? '' : new Twig_Markup($tmp, $this->env->getCharset());
    }

    public function getTemplateName()
    {
        return "partials/sidebar.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  212 => 33,  208 => 31,  204 => 29,  202 => 28,  196 => 27,  193 => 26,  190 => 25,  179 => 24,  155 => 20,  149 => 17,  146 => 16,  144 => 15,  134 => 13,  129 => 11,  121 => 10,  118 => 9,  115 => 8,  97 => 7,  94 => 6,  91 => 5,  88 => 4,  85 => 3,  82 => 2,  70 => 1,  60 => 49,  54 => 46,  51 => 45,  41 => 41,  37 => 40,  32 => 39,  27 => 38,  25 => 37,  22 => 36,  19 => 23,);
    }
}
