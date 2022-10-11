<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;

/* themes/singleportal/templates/page.html.twig */
class __TwigTemplate_315635bd93f8e49e23333633a57597e6c6f4f3163073fd73e329b9d6dce06893 extends \Twig\Template
{
    private $source;
    private $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->env->getExtension('\Twig\Extension\SandboxExtension');
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = [])
    {
        $macros = $this->macros;
        // line 1
        echo "  <div id=\"wrapper\">
      <nav class=\"navbar navbar-default navbar-static-top m-b-0\">
            <div class=\"navbar-header\">
                
                    ";
        // line 5
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "header", [], "any", false, false, true, 5), 5, $this->source), "html", null, true);
        echo "
\t\t\t\t
            </div>
            <!-- /.navbar-header -->
            <!-- /.navbar-top-links -->
            <!-- /.navbar-static-side -->
        </nav>
        <!-- End Top Navigation -->
        <!-- ============================================================== -->
        <!-- Left Sidebar - style you can find in sidebar.scss  -->
        <!-- ============================================================== -->
       
\t   ";
        // line 17
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "mainmenu", [], "any", false, false, true, 17), 17, $this->source), "html", null, true);
        echo "
        
\t  
        <div id=\"page-wrapper\">
            <div class=\"container-fluid\">
\t";
        // line 22
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "breadcrumb", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
        echo "
\t\t\t<main>
\t\t\t\t<!-- .row -->
                <div class=\"row\">
                    <div class=\"col-md-12\">
                      ";
        // line 27
        $context["url"] = $this->extensions['Drupal\Core\Template\TwigExtension']->getUrl("<current>");
        // line 28
        echo "                      ";
        if (twig_in_filter("home", $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["url"] ?? null))))) {
            // line 29
            echo "\t\t\t\t\t\t<div class=\"col-lg-6\">
                        ";
            // line 30
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "dashleft", [], "any", false, false, true, 30), 30, $this->source), "html", null, true);
            echo "
\t\t\t\t\t\t</div>
\t\t\t\t\t\t<div class=\"col-lg-6\">
\t\t\t\t\t\t";
            // line 33
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "dashright", [], "any", false, false, true, 33), 33, $this->source), "html", null, true);
            echo "
\t\t\t\t\t\t<div class=\"col-lg-12 col-sm-12 col-xs-12\">
                        <div class=\"news-slide m-b-30 dashboard-slide\">
                            <div class=\"vcarousel slide\">
                                <!-- Carousel items -->
                                <div class=\"carousel-inner\">
                                    <div class=\"active item\">
                                        <div class=\"overlaybg\"><img src=\"../plugins/images/heading-bg/slide6.jpg\"></div>
                                        <div class=\"news-content\"><span class=\"label label-danger label-rounded\">Primary</span>
                                            <h2>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</h2> <a href=\"javascript:void(0)\">Read More</a></div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"overlaybg\"><img src=\"../plugins/images/heading-bg/slide4.jpg\"></div>
                                        <div class=\"news-content\"><span class=\"label label-primary label-rounded\">Primary</span>
                                            <h2>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</h2> <a href=\"javascript:void(0)\">Read More</a></div>
                                    </div>
                                    <div class=\"item\">
                                        <div class=\"overlaybg\"><img src=\"../plugins/images/heading-bg/slide6.jpg\"></div>
                                        <div class=\"news-content\"><span class=\"label label-success label-rounded\">Primary</span>
                                            <h2>It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged.</h2> <a href=\"javascript:void(0)\">Read More</a></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
\t\t\t\t\t\t</div>
                      ";
        } elseif (        // line 59
($context["white_box_exclud"] ?? null)) {
            // line 60
            echo "\t\t\t\t\t\t  ";
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "message", [], "any", false, false, true, 60), 60, $this->source), "html", null, true);
            echo "
\t\t\t\t\t\t  ";
            // line 61
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 61), 61, $this->source), "html", null, true);
            echo "
\t\t\t\t\t  ";
        } else {
            // line 63
            echo "                        <div class=\"white-box\">                      
\t\t\t\t\t\t  ";
            // line 64
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "message", [], "any", false, false, true, 64), 64, $this->source), "html", null, true);
            echo "
\t\t\t\t\t\t  ";
            // line 65
            echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "content", [], "any", false, false, true, 65), 65, $this->source), "html", null, true);
            echo "
                        </div>
\t\t\t\t\t ";
        }
        // line 68
        echo "                    </div>
                </div>
                <!-- .row -->
\t\t\t</main>\t
\t\t\t
\t\t\t</div>
\t\t\t
\t\t\t";
        // line 75
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "footer", [], "any", false, false, true, 75), 75, $this->source), "html", null, true);
        echo "
            
        </div>
        <!-- ============================================================== -->
        <!-- End Page Content -->
        <!-- ============================================================== -->
    </div>
  
";
    }

    public function getTemplateName()
    {
        return "themes/singleportal/templates/page.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  153 => 75,  144 => 68,  138 => 65,  134 => 64,  131 => 63,  126 => 61,  121 => 60,  119 => 59,  90 => 33,  84 => 30,  81 => 29,  78 => 28,  76 => 27,  68 => 22,  60 => 17,  45 => 5,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/singleportal/templates/page.html.twig", "/app/themes/singleportal/templates/page.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("set" => 27, "if" => 28);
        static $filters = array("escape" => 5, "render" => 28);
        static $functions = array("url" => 27);

        try {
            $this->sandbox->checkSecurity(
                ['set', 'if'],
                ['escape', 'render'],
                ['url']
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
