package com.detectivecase.model;

import lombok.Data;

@Data
public class HeatmapData {
  private Double lat;
  private Double lng;
  private Integer weight;
}