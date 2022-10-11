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

/* themes/singleportal/templates/page--front.html.twig */
class __TwigTemplate_2b04991402d09ed55e4818a639b58abe558454eed4fa4a4d363f812f094a74a7 extends \Twig\Template
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
        echo "
  <div class=\"page\" data-animsition-in=\"fade-in\" data-animsition-out=\"fade-out\">
    <div class=\"page-content\">
      <div class=\"page-brand-info\">
        <!-- <div class=\"brand\"> -->
          <!-- <img class=\"brand-img\" src=\"";
        // line 6
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["site_logo"] ?? null), 6, $this->source), "html", null, true);
        echo "\" alt=\"...\"> -->
          <!-- <h2 class=\"brand-text font-size-40\">";
        // line 7
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["site_name"] ?? null), 7, $this->source), "html", null, true);
        echo "</h2>  -->
        <!-- </div> -->
        <p class=\"font-size-20\">Single Signin Place where all the action is!<br/>
          One account is all you need to do all action.</p>
      </div>

      <div class=\"page-login-main animation-slide-right animation-duration-1\">
        <div class=\"brand hidden-md-up\">
          <img class=\"brand-img\" src=\"";
        // line 15
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["site_logo"] ?? null), 15, $this->source), "html", null, true);
        echo "\" alt=\"...\">
          <!-- <h3 class=\"brand-text font-size-40\">Remark</h3> -->
        </div>
\t\t<div class=\"brand\">
          <img class=\"brand-img\" src=\"";
        // line 19
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["site_logo"] ?? null), 19, $this->source), "html", null, true);
        echo "\" alt=\"...\">
        </div>
        <h3 class=\"font-size-24\">Sign In</h3>
\t\t";
        // line 22
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(twig_get_attribute($this->env, $this->source, ($context["page"] ?? null), "message", [], "any", false, false, true, 22), 22, $this->source), "html", null, true);
        echo "
        <form method=\"post\" action=\"";
        // line 23
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["base_path"] ?? null), 23, $this->source), "html", null, true);
        echo "user/login\" id=\"user-login-form\" accept-charset=\"UTF-8\" data-drupal-selector=\"user-login-form\" autocomplete=\"off\">
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"inputEmail\">Email</label>
            <input type=\"text\" class=\"form-control\" id=\"edit-name\" name=\"name\" required=\"true\" placeholder=\"Username\">
          </div>
          <div class=\"form-group\">
            <label class=\"sr-only\" for=\"inputPassword\">Password</label>
            <input type=\"password\" class=\"form-control\" id=\"edit-pass\" required=\"true\" name=\"pass\"
              placeholder=\"Password\">
          </div>
          <div class=\"form-group clearfix\">
            
            <a class=\"float-right\" href=\"#\">Forgot password?</a>
          </div>
\t\t  <input data-drupal-selector=\"form-r3v-romeeujsmbybxohwpxjitxzw7uzcxjmqmuiisem\" name=\"form_build_id\" value=\"form-r3v-rOMeeujsmbYBXOhWPxJITXzW7UZcXjMQmUiiSeM\" type=\"hidden\">
   \t\t  <input data-drupal-selector=\"edit-user-login-form\" name=\"form_id\" value=\"user_login_form\" type=\"hidden\">

          <button type=\"submit\" name=\"op\" id=\"edit-submit\" class=\"btn btn-primary btn-block\">Login</button>
        </form>


        <footer class=\"page-copyright\">
          <p>Entry to this site is restricted to Employee
          <br/>© ";
        // line 46
        echo $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, $this->sandbox->ensureToStringAllowed(($context["year"] ?? null), 46, $this->source), "html", null, true);
        echo ". All RIGHT RESERVED.</p>
          
        </footer>
      </div>

    </div>
  </div>
  <!-- End Page -->

 

";
    }

    public function getTemplateName()
    {
        return "themes/singleportal/templates/page--front.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  104 => 46,  78 => 23,  74 => 22,  68 => 19,  61 => 15,  50 => 7,  46 => 6,  39 => 1,);
    }

    public function getSourceContext()
    {
        return new Source("", "themes/singleportal/templates/page--front.html.twig", "/app/themes/singleportal/templates/page--front.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array();
        static $filters = array("escape" => 6);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                [],
                ['escape'],
                []
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
