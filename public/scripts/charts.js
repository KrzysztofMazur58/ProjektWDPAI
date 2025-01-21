let consumedCalories = 0;
let remainingCalories = 0;
let myChart = null;
let dailyCalories = 0;
let userId = 0;

let consumedProtein = 0;
let consumedFat = 0;
let consumedCarbs = 0;

let dailyProtein = 0;
let dailyFat = 0;
let dailyCarbs = 0;

let servingWeight = 0;

let charts = {};

function fetchUserData() {
    fetch('getUserData.php', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error('Error fetching data:', data.error);
                alert('Unable to load user data.');
                return;
            }

            dailyCalories = data.daily_calories || 0;
            consumedCalories = data.consumed_calories || 0;
            dailyProtein = data.daily_protein || 0;
            consumedProtein = data.consumed_protein || 0;
            dailyFat = data.daily_fat || 0;
            consumedFat = data.consumed_fat || 0;
            dailyCarbs = data.daily_carbs || 0;
            consumedCarbs = data.consumed_carbs || 0;

            remainingCalories = Math.max(0, dailyCalories - consumedCalories);

            document.getElementById("remainingCalories").innerText =
                `${Math.round(remainingCalories)} / ${Math.round(dailyCalories)}`;

            updateChart();
            updateMacronutrientCharts();
            getMacronutrientAdvice();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was a problem fetching user data.');
        });
}

function saveConsumedCalories() {
    fetch('updateCalories.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            user_id: userId,
            consumedCalories,
            consumedProtein,
            consumedFat,
            consumedCarbs
        })
    })
        .then(response => response.json())
        .then(data => {
            if (!data.success) {
                alert('Error saving data.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was a problem saving data.');
        });
}

function initChart() {
    const ctx = document.getElementById('calorieChart').getContext('2d');
    if (myChart) myChart.destroy();

    myChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                label: 'Calories',
                data: [Math.round(consumedCalories), Math.round(remainingCalories)],
                backgroundColor: ['#3AA0FE', '#0F0872'],
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { enabled: true }
            }
        }
    });
}

function updateChart() {
    if (myChart) {
        myChart.data.datasets[0].data = [Math.round(consumedCalories), Math.round(remainingCalories)];
        myChart.update();
    }
}

function initMacronutrientCharts() {
    updateSingleChart('proteinChart', consumedProtein, dailyProtein, '#3AA0FE', '#0F0872');
    updateSingleChart('fatChart', consumedFat, dailyFat, '#3AA0FE', '#0F0872');
    updateSingleChart('carbsChart', consumedCarbs, dailyCarbs, '#3AA0FE', '#0F0872');
}

function updateMacronutrientCharts() {
    updateSingleChart('proteinChart', consumedProtein, dailyProtein, '#3AA0FE', '#0F0872');
    updateSingleChart('fatChart', consumedFat, dailyFat, '#3AA0FE', '#0F0872');
    updateSingleChart('carbsChart', consumedCarbs, dailyCarbs, '#3AA0FE', '#0F0872');
}

function updateSingleChart(chartId, consumed, daily, color1, color2) {
    const ctx = document.getElementById(chartId).getContext('2d');
    if (charts[chartId]) charts[chartId].destroy();

    charts[chartId] = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                label: chartId,
                data: [consumed, Math.max(0, daily - consumed)],
                backgroundColor: [color1, color2],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'top' },
                tooltip: { enabled: true }
            }
        }
    });
}

function addProduct() {
    const productQuery = document.getElementById("productQuery").value;
    const mealWeight = document.getElementById("mealWeight").value;

    if (!productQuery || !mealWeight) {
        alert("Please provide a product name and meal weight.");
        return;
    }

    fetch('getCalories.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ query: productQuery })
    })
        .then(response => response.json())
        .then(data => {
            if (data.calories !== undefined) {
                const apiServingWeight = data.serving_weight_grams || 0;
                const apiCalories = data.calories || 0;
                const apiProtein = data.protein || 0;
                const apiFat = data.fat || 0;
                const apiCarbs = data.carbohydrates || 0;

                if (apiServingWeight > 0) {
                    const scaleFactor = mealWeight / apiServingWeight;

                    consumedCalories += Math.round(apiCalories * scaleFactor);
                    consumedProtein += Math.round(apiProtein * scaleFactor);
                    consumedFat += Math.round(apiFat * scaleFactor);
                    consumedCarbs += Math.round(apiCarbs * scaleFactor);

                    servingWeight = mealWeight;
                }

                remainingCalories = Math.max(0, dailyCalories - consumedCalories);

                document.getElementById("remainingCalories").innerText =
                    `${Math.round(remainingCalories)} / ${Math.round(dailyCalories)}`;

                saveConsumedCalories();
                updateChart();
                updateMacronutrientCharts();
                getMacronutrientAdvice();

                const addedMealMessage = document.getElementById("addedMealMessage");
                addedMealMessage.innerText = `Added product: ${productQuery}, serving weight: ${servingWeight}g`;
                addedMealMessage.style.display = "block";
            } else {
                alert('Product not found.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('There was a problem adding the product.');
        });
}

function getNutritionTip() {
    const hour = new Date().getHours();
    let tip = "";
    let benefit = "";

    if (hour >= 6 && hour < 10) {
        tip = "🍳 Start your day with a high-protein breakfast!";
        benefit = "✅ Boosts muscle mass and keeps you full.";
    } else if (hour >= 10 && hour < 14) {
        tip = "🥗 Time for a balanced lunch!";
        benefit = "✅ Stabilizes energy and supports focus.";
    } else if (hour >= 14 && hour < 18) {
        tip = "🕒 Mid-afternoon snack!";
        benefit = "✅ Prevents energy crashes.";
    } else if (hour >= 18 && hour < 21) {
        tip = "🍽️ Dinner should be light and easy to digest.";
        benefit = "✅ Promotes better sleep.";
    } else {
        tip = "🌙 Avoid heavy meals late at night.";
        benefit = "✅ Supports better sleep quality.";
    }

    document.getElementById("nutritionTip").innerText = tip;
    document.getElementById("nutritionBenefit").innerText = benefit;
}

function getMacronutrientAdvice() {
    let advice = "";
    const hour = new Date().getHours();

    // Upewnij się, że dane są dostępne, zanim obliczymy porady
    if (dailyProtein && dailyFat && dailyCarbs && consumedProtein >= 0 && consumedFat >= 0 && consumedCarbs >= 0) {

        const proteinPercentage = (consumedProtein / dailyProtein) * 100;
        const fatPercentage = (consumedFat / dailyFat) * 100;
        const carbsPercentage = (consumedCarbs / dailyCarbs) * 100;
        let isOver;

        console.log('Protein:', proteinPercentage, 'Fat:', fatPercentage, 'Carbs:', carbsPercentage);

        if (proteinPercentage > 100) {
            isOver = true;
            advice += "💪 Your protein intake is over 100%. You may want to adjust your protein intake to avoid excess.";
        }
        if (fatPercentage > 100) {
            isOver = true;
            advice += "\n🥑 Your fat intake is over 100%. Be cautious with your fat intake to prevent overconsumption.";
        }
        if (carbsPercentage > 100) {
            isOver = true;
            advice += "\n🍞 Your carbohydrate intake is over 100%. Too many carbs can affect your overall balance.";
        }

        if (hour >= 13 && hour < 18) {

            if (proteinPercentage < 30) {
                advice += "💪 Your protein intake is below 30%. Consider adding more protein-rich foods like lean meats or legumes.";
            }
            if (fatPercentage < 30) {
                advice += "\n🥑 Your fat intake is below 30%. Try including healthy fats like avocados, nuts, and olive oil.";
            }
            if (carbsPercentage < 30) {
                advice += "\n🍞 Your carbohydrate intake is below 30%. Consider adding more whole grains, fruits, and vegetables.";
            }

            if (proteinPercentage > 60 && proteinPercentage <= 100) {
                advice += "\n💪 Your protein intake is above 60%. Ensure you're not overdoing it!";
            }
            if (fatPercentage > 60 && fatPercentage <= 100) {
                advice += "\n🥑 Your fat intake is above 60%. Make sure to balance your fat intake with healthy options.";
            }
            if (carbsPercentage > 60 && carbsPercentage <= 100) {
                advice += "\n🍞 Your carbohydrate intake is above 60%. Keep an eye on your carb intake to avoid excess.";
            }

        } else if (hour >= 18) {

            if (proteinPercentage < 70) {
                advice += "💪 Your protein intake is below 70%. Make sure to include more protein sources before the day ends.";
            }
            if (fatPercentage < 70) {
                advice += "\n🥑 Your fat intake is below 70%. Ensure you're getting enough healthy fats.";
            }
            if (carbsPercentage < 70) {
                advice += "\n🍞 Your carbohydrate intake is below 70%. Add more carbs from whole grains, fruits, or vegetables.";
            }


        } else if(!isOver){

            advice += "\n✅ You're on track with your macronutrients for the day!";
        }

    } else if (hour >= 13 && (dailyProtein === 0 || dailyFat === 0 || dailyCarbs === 0)) {

        if (dailyProtein === 0) {
            advice += "💪 Your protein intake is below 30%. Consider adding more protein-rich foods like lean meats or legumes.";
        }
        if (dailyFat === 0) {
            advice += "\n🥑 Your fat intake is below 30%. Try including healthy fats like avocados, nuts, and olive oil.";
        }
        if (dailyCarbs === 0) {
            advice += "\n🍞 Your carbohydrate intake is below 30%. Consider adding more whole grains, fruits, and vegetables.";
        }
    } else {

        advice = "❗ Data is missing or invalid. Please try again.";
    }

    console.log('Macronutrient Advice:', advice);
    document.getElementById("macronutrientAdvice").innerText = advice;
}

window.onload = function () {
    initChart();
    fetchUserData();
    initMacronutrientCharts();
    getNutritionTip();
};

// Add event listener for the confirm button
document.getElementById("confirmButton").addEventListener("click", addProduct);
