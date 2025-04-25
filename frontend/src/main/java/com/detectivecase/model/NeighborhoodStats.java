package com.detectivecase.model;

import lombok.Data;

@Data
public class NeighborhoodStats {
  private String borough;
  private Integer totalCrimes;
  private Integer theftCount;
  private Integer assaultCount;
  private Integer burglaryCount;
}