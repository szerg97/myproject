<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class TsvController
 * @package App\Controller
 * @Route(path="recipebook/")
 */
class RecipeBookController extends AbstractController{

    const PATH_INGREDIENTS = "../templates/recipebook/ingredients.txt";
    const PATH_RECIPES = "../templates/recipebook/recipes.txt";
    const TWIG_FORM = "recipebook/form.html.twig";
    const TWIG_FORM_INPUTS = "recipebook/forminputs.html.twig";
    const TWIG_RECIPES = "recipebook/recipes.html.twig";
    const TWIG_INGREDIENTS = "recipebook/ingredients.html.twig";


    private function readIngredientsDatabase(): array{
        $lines = file(self::PATH_INGREDIENTS);
        return $lines;
    }

    private function readRecipesDatabase(): array{
        $lines = file(self::PATH_RECIPES);
        return $lines;
    }

    /**
     *  @Route(path="form", name="getRecipeForm")
     */
    public function getRecipeForm(){
        $twig_params = ["ingredients" => $this->readIngredientsDatabase()];
        return $this->render(self::TWIG_FORM, $twig_params);
    }

    /**
     *  @Route(path="form/inputs", name="getRecipeFormInputs")
     */
    public function getRecipeFormInputs(Request $request){
        $name = $request->request->get("entry_name");
        $number = $request->request->getInt("entry_ingredient_number");
        if ($name && $number) {
            $this->ingCount = $number;
            $details = array("name" => $name, "number" => $number);
            $twig_params = ["details" => $details, "ingredients" => $this->readIngredientsDatabase()];
        }
        
        return $this->render(self::TWIG_FORM_INPUTS, $twig_params);
    }

    /**
     *  @Route(path="ingredients/new", name="addIngredient")
     */
    public function addIngredient(Request $request){
        $ingredient = $request->request->get("ingredient");
        if ($ingredient) {
            file_put_contents(self::PATH_INGREDIENTS, "\n$ingredient", FILE_APPEND);
        }
        return $this->redirectToRoute("getIngredientsList");
    }

    /**
     *  @Route(path="recipes/new", name="addRecipe")
     */
    public function addRecipe(Request $request){
        $name = $request->request->get("name");
        $count = $request->request->get("count");
        $name = str_replace(' ', '_', $name);

        file_put_contents(self::PATH_RECIPES, strtoupper("$name|"), FILE_APPEND);
        for ($i=1; $i < $count+1; $i++) {
            $ingredient = $request->request->get("entry_ingredient$i");
            $amount = $request->request->getInt("amount$i");
            file_put_contents(self::PATH_RECIPES, "$ingredient-", FILE_APPEND);
            if ($i == $count) file_put_contents(self::PATH_RECIPES, "$amount", FILE_APPEND);
            else file_put_contents(self::PATH_RECIPES, "$amount;", FILE_APPEND);
        }
        file_put_contents(self::PATH_RECIPES, "\n", FILE_APPEND);
        return $this->redirectToRoute("getRecipesList");
    }

    /**
     *  @Route(path="ingredients", name="getIngredientsList")
     */
    public function getIngredientsList(){
        $db = $this->readIngredientsDatabase();
        $twig_params = ["ingredients" => $db];
        return $this->render(self::TWIG_INGREDIENTS, $twig_params);
    }

    /**
     *  @Route(path="recipes", name="getRecipesList")
     */
    public function getRecipesList(){
        $db = $this->readRecipesDatabase();

        $twig_params = ["recipes" => array()];
        $recipe = ["name"=>"", "ingredients"=>""];

        foreach ($db as $line) {
            $pos = strpos($line, "|");
            $name = substr($line, 0, $pos);
            $ingrs = substr($line, $pos + 1);

            $recipe = ["name"=>"", "ingredients"=>""];
            $recipe["name"] = $name;
            $recipe["ingredients"] = $ingrs;

            $twig_params["recipes"][] = $recipe;
        }
        
        return $this->render(self::TWIG_RECIPES, $twig_params);
    }
}