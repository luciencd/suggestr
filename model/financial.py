def get_max_profit(stock_prices):
    max_deal = []
    min_deal = []
    best_deal = []
    
    current_max = stock_prices[0]
    current_min = stock_prices[0]
    best = 0
    for i in range(len(stock_prices)):
        if(stock_prices[i] > current_max):
            current_max = stock_prices[i]
            max_deal.append(stock_prices[i])
            min_deal.append(current_min)
        elif(stock_prices[i] < current_min):
            current_min = stock_prices[i]
            max_deal.append(current_min)
            min_deal.append(stock_prices[i])
            current_max = stock_prices[i]

        if(current_max-current_min > best):
            best = current_max-current_min
        
        best_deal.append(current_max-current_min)
        
    print min_deal,max_deal,best_deal
    
    
    return best
            
        

stock_prices_yesterday = [10, 7, 5, 8, 11, 9]
#max_deal = [10,10,5,8,11,11]
#min_deal = [10,7,5,5,5,5]
#best_deal = [0,3,0,3,6,6]
print get_max_profit(stock_prices_yesterday)
