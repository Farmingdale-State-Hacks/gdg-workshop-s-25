package com.detectivecase.service;

import com.detectivecase.model.CrimeData;
import com.detectivecase.model.CrimeType;
import com.detectivecase.model.HeatmapData;
import com.detectivecase.model.NeighborhoodStats;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.core.ParameterizedTypeReference;
import org.springframework.stereotype.Service;
import org.springframework.web.reactive.function.client.WebClient;
import reactor.core.publisher.Mono;

import java.time.Duration;
import java.util.List;
import java.util.Map;

@Service
public class CrimeDataService {

  private final WebClient webClient;

  public CrimeDataService(@Value("${api.base.url:http://localhost:8001/api}") String apiBaseUrl) {
    this.webClient = WebClient.builder()
        .baseUrl(apiBaseUrl)
        .build();
  }

  public Mono<Map<String, Object>> getCrimes(String type, String startDate, String endDate, Integer limit) {
    return webClient.get()
        .uri(uriBuilder -> uriBuilder
            .path("/crimes")
            .queryParam("type", type)
            .queryParam("start_date", startDate)
            .queryParam("end_date", endDate)
            .queryParam("limit", limit)
            .build())
        .retrieve()
        .bodyToMono(new ParameterizedTypeReference<Map<String, Object>>() {
        })
        .timeout(Duration.ofSeconds(10));
  }

  public Mono<Map<String, Object>> getCrimeTypes() {
    return webClient.get()
        .uri("/crime-types")
        .retrieve()
        .bodyToMono(new ParameterizedTypeReference<Map<String, Object>>() {
        })
        .timeout(Duration.ofSeconds(10));
  }

  public Mono<Map<String, Object>> getCrimeHeatmap(String type, String period) {
    return webClient.get()
        .uri(uriBuilder -> uriBuilder
            .path("/crime-heatmap")
            .queryParam("type", type)
            .queryParam("period", period)
            .build())
        .retrieve()
        .bodyToMono(new ParameterizedTypeReference<Map<String, Object>>() {
        })
        .timeout(Duration.ofSeconds(10));
  }

  public Mono<Map<String, Object>> getNeighborhoodStats() {
    return webClient.get()
        .uri("/neighborhood-stats")
        .retrieve()
        .bodyToMono(new ParameterizedTypeReference<Map<String, Object>>() {
        })
        .timeout(Duration.ofSeconds(10));
  }
}