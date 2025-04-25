package com.detectivecase.controller;

import com.detectivecase.service.CrimeDataService;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.RequestMapping;
import org.springframework.web.bind.annotation.RequestParam;
import org.springframework.web.bind.annotation.RestController;
import reactor.core.publisher.Mono;

import java.util.Map;

@RestController
@RequestMapping("/api")
public class ApiController {

  private final CrimeDataService crimeDataService;

  @Autowired
  public ApiController(CrimeDataService crimeDataService) {
    this.crimeDataService = crimeDataService;
  }

  @GetMapping("/crimes")
  public Mono<Map<String, Object>> getCrimes(
      @RequestParam(required = false, defaultValue = "ALL") String type,
      @RequestParam(required = false, defaultValue = "2019-01-01") String startDate,
      @RequestParam(required = false, defaultValue = "2023-01-01") String endDate,
      @RequestParam(required = false, defaultValue = "1000") Integer limit) {

    return crimeDataService.getCrimes(type, startDate, endDate, limit);
  }

  @GetMapping("/crime-types")
  public Mono<Map<String, Object>> getCrimeTypes() {
    return crimeDataService.getCrimeTypes();
  }

  @GetMapping("/crime-heatmap")
  public Mono<Map<String, Object>> getCrimeHeatmap(
      @RequestParam(required = false, defaultValue = "ALL") String type,
      @RequestParam(required = false, defaultValue = "pre-covid") String period) {

    return crimeDataService.getCrimeHeatmap(type, period);
  }

  @GetMapping("/neighborhood-stats")
  public Mono<Map<String, Object>> getNeighborhoodStats() {
    return crimeDataService.getNeighborhoodStats();
  }
}