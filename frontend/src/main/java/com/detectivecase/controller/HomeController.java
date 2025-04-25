package com.detectivecase.controller;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Controller;
import org.springframework.ui.Model;
import org.springframework.web.bind.annotation.GetMapping;

@Controller
public class HomeController {

  @Value("${api.base.url:http://localhost:8001/api}")
  private String apiBaseUrl;

  @GetMapping("/")
  public String home(Model model) {
    model.addAttribute("apiBaseUrl", apiBaseUrl);
    return "index";
  }

  @GetMapping("/heatmap")
  public String heatmap(Model model) {
    model.addAttribute("apiBaseUrl", apiBaseUrl);
    return "heatmap";
  }

  @GetMapping("/dashboard")
  public String dashboard(Model model) {
    model.addAttribute("apiBaseUrl", apiBaseUrl);
    return "dashboard";
  }

  @GetMapping("/about")
  public String about() {
    return "about";
  }
}