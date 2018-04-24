<?php

/* index.html */
class __TwigTemplate_7db75dc4ccc2e8eab26d9305f07b203dfba239c07015bff710ecb9a8abb0c6c7 extends Twig_Template
{
    private $source;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = array(
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!doctype html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\"
          content=\"width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0\">
    <meta http-equiv=\"X-UA-Compatible\" content=\"ie=edge\">
    <title>";
        // line 8
        echo twig_escape_filter($this->env, ($context["title"] ?? null), "html", null, true);
        echo "</title>
</head>
<body>
";
        // line 11
        echo twig_escape_filter($this->env, ($context["desc"] ?? null), "html", null, true);
        echo "
</body>
</html>";
    }

    public function getTemplateName()
    {
        return "index.html";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  38 => 11,  32 => 8,  23 => 1,);
    }

    public function getSourceContext()
    {
        return new Twig_Source("", "index.html", "F:\\php\\www\\mvc\\app\\view\\index.html");
    }
}
